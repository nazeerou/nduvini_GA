<?php

namespace App\Http\Controllers;

use App\Services\NextSmsService;
use App\Models\PayrollRequest;
use App\Models\PayrollRequestEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class PayrollRequestController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $type     = $request->get('type');       // monthly_salary | salary_advance
        $status   = $request->get('status');     // pending | approved | etc.

        $query = PayrollRequest::forBranch($branchId)
            ->with(['employee', 'requester', 'approver'])
            ->orderByDesc('requested_at');

        if ($type)   $query->where('type', $type);
        if ($status) $query->where('status', $status);

        $requests = $query->paginate(20);

        // Counters for the tabs
        $counts = [
            'pending'         => PayrollRequest::forBranch($branchId)->where('status','pending')->count(),
            'approved_this_m' => PayrollRequest::forBranch($branchId)
                                    ->where('status','approved')
                                    ->whereMonth('approved_at', now()->month)
                                    ->whereYear('approved_at', now()->year)
                                    ->count(),
        ];

        return view('payrolls.requests.index', compact('requests', 'counts', 'type', 'status'));
    }

    /**
     * Per-month approval summary used by the new "Requests" tab.
     * Returns rows like:
     *   [ 'period' => 'Jun 2024',
     *     'monthly_salary' => ['status'=>'approved','count'=>1,'amount'=>5000000],
     *     'salary_advance' => ['status'=>'pending','count'=>2,'amount'=>300000],
     *     'actions' => ... ]
     */
    public function monthlySummary()
    {
        $branchId = Auth::user()->branch_id;

        $rows = PayrollRequest::forBranch($branchId)
            ->selectRaw('period_year, period_month, type, status,
                         COUNT(*) AS c, SUM(amount) AS total')
            ->whereIn('status', ['pending','approved','rejected'])
            ->whereNotNull('period_year')
            ->groupBy('period_year','period_month','type','status')
            ->get();

        // Pivot into per-period buckets
        $periods = [];
        foreach ($rows as $r) {
            $key = $r->period_year . '-' . str_pad($r->period_month, 2, '0', STR_PAD_LEFT);
            $periods[$key] ??= [
                'period'          => Carbon::create($r->period_year, $r->period_month, 1)->format('M Y'),
                'monthly_salary'  => null,
                'salary_advance'  => null,
            ];
            $periods[$key][$r->type] = [
                'status' => $r->status,
                'count'  => (int) $r->c,
                'amount' => (float) $r->total,
            ];
        }

        krsort($periods);
        return response()->json(['periods' => array_values($periods)]);
    }

    public function store(Request $request)
    {
        /*
        |------------------------------------------------------------------
        | 1. Validation — employee_id / send_to must be SCALARS,
        |    payload must be an ARRAY (cast handles JSON encoding)
        |------------------------------------------------------------------
        */
        $data = $request->validate([
            'type'             => 'required|in:monthly_salary,salary_advance',
            'period_month'     => 'required|integer|between:1,12',
            'period_year'      => 'required|integer|min:2020',

            // bulk monthly payroll sends no employee_id; advances send one
            'employee_id'      => 'nullable|integer|exists:employees,id',

            // single approver — scalar only
            'send_to'          => 'required_unless:decision,accepted,rejected|nullable|integer|exists:users,id',

            'amount'           => 'required|numeric|min:0.01',
            'employee_count'   => 'required|integer|min:1',

            // payload is an object/array — model casts to JSON
            'payload'          => 'nullable|array',

            'notes'            => 'nullable|string|max:1000',
            'decision'         => 'nullable|in:accepted,rejected',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $user          = Auth::user();
        $branchId      = $user->branch_id;
        $requesterName = $user->name;

        /*
        |------------------------------------------------------------------
        | 2. Normalise to scalars — defensive against multi-selects
        |------------------------------------------------------------------
        */
        $approverId = $this->scalarInt($data['send_to']     ?? null);
        $employeeId = $this->scalarInt($data['employee_id'] ?? null);

        // monthly_salary is BULK → never store a single employee_id
        if ($data['type'] !== 'salary_advance') {
            $employeeId = null;
        }

        $decision = $data['decision'] ?? null;

        $period = sprintf('%04d-%02d', $data['period_year'], $data['period_month']);

        /*
        |------------------------------------------------------------------
        | 3. Build a clean payload array
        |    (column has CHECK json_valid — always send valid JSON or NULL)
        |------------------------------------------------------------------
        */
        $payload = is_array($data['payload'] ?? null) ? $data['payload'] : [];

        $payload = array_merge($payload, [
            'period'         => $period,                                            // "2026-10"
            'employee_ids'   => $request->input('employee_ids', $payload['employee_ids'] ?? []),
            'total_amount'   => (float) $data['amount'],                            // 10820000.0
            'employee_count' => (int)   $data['employee_count'],                    // 25
        ]);

        /*
        |------------------------------------------------------------------
        | 4. Payroll reference for the period
        |------------------------------------------------------------------
        */
        $payrollReference = DB::table('payrolls')
            ->where('branch_id', $branchId)
            ->where('month', $period)
            ->value('reference');

        /*
        |------------------------------------------------------------------
        | 5. Find existing request for this branch + type + period
        |------------------------------------------------------------------
        */
        $existing = PayrollRequest::forBranch($branchId)
            ->where('type', $data['type'])
            ->where('period_year', $data['period_year'])
            ->where('period_month', $data['period_month'])
            ->whereIn('status', ['pending', 'approved', 'rejected', 'cancelled'])
            ->latest('id')
            ->first();

        /*
        |------------------------------------------------------------------
        | 6. Approved request — cannot overwrite
        |------------------------------------------------------------------
        */
        if ($existing && $existing->status === 'approved') {
            return response()->json([
                'errors' => [
                    'type' => ['A request for this period is already approved.'],
                ],
            ], 422);
        }

        /*
        |------------------------------------------------------------------
        | 7. Existing PENDING request
        |------------------------------------------------------------------
        */
        if ($existing && $existing->status === 'pending') {

            // 7a. Approve / reject the pending request
            if (in_array($decision, ['accepted', 'rejected'], true)) {

                $newStatus   = $decision === 'accepted' ? 'approved' : 'rejected';
                $eventAction = $newStatus;
                $eventNote   = $data['notes']
                    ?? ($decision === 'accepted' ? 'Request approved' : 'Request rejected');

                DB::transaction(function () use ($existing, $data, $decision, $newStatus, $eventAction, $eventNote) {
                    $update = [
                        'status'      => $newStatus,
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ];

                    if ($decision === 'rejected') {
                        $update['rejected_by']      = Auth::id();
                        $update['rejected_at']      = now();
                        $update['rejection_reason'] = $data['rejection_reason']
                            ?? ($data['notes'] ?? 'Rejected by approver');
                    }

                    $existing->update($update);

                    PayrollRequestEvent::create([
                        'payroll_request_id' => $existing->id,
                        'action'             => $eventAction,
                        'actor_id'           => Auth::id(),
                        'notes'              => $eventNote,
                    ]);
                });

                return response()->json([
                    'message' => $decision === 'accepted'
                        ? 'Request approved.'
                        : 'Request rejected.',
                    'request' => $existing->fresh(),
                    'status'  => $newStatus,
                ]);
            }

            // 7b. Update and resubmit pending request
            if (!$approverId) {
                return response()->json([
                    'errors' => ['send_to' => ['Please select an approver.']],
                ], 422);
            }

            DB::transaction(function () use ($existing, $employeeId, $approverId, $data, $payload) {
                $existing->update([
                    'employee_id'    => $employeeId,           // scalar or null
                    'approver_id'    => $approverId,           // scalar
                    'amount'         => $data['amount'],
                    'employee_count' => (int) $data['employee_count'],
                    'notes'          => $data['notes'] ?? null,
                    'payload'        => $payload,              // cast → JSON string
                    'requested_by'   => Auth::id(),
                    'requested_at'   => now(),
                ]);

                PayrollRequestEvent::create([
                    'payroll_request_id' => $existing->id,
                    'action'             => 'updated',
                    'actor_id'           => Auth::id(),
                    'notes'              => 'Pending request updated and resubmitted for approval',
                ]);
            });

            $smsMessage =
                "PAYROLL REQUEST UPDATED\n" .
                "Requester: {$requesterName}\n" .
                "Message: " . ($data['notes'] ?? 'No message provided');

            $smsSent = $this->notifyPayrollApprover(
                $approverId,
                $smsMessage,
                'payroll-' . $existing->id . '-upd-' . now()->format('YmdHis')
            );

            return response()->json([
                'message'  => 'Pending request updated.',
                'request'  => $existing->fresh(),
                'updated'  => true,
                'sms_sent' => $smsSent,
            ]);
        }

        /*
        |------------------------------------------------------------------
        | 8. Create NEW request
        |------------------------------------------------------------------
        */
        $newStatus   = 'pending';
        $eventAction = 'created';
        $eventNote   = 'Request submitted for approval';

        if ($decision === 'accepted') {
            $newStatus   = 'approved';
            $eventAction = 'approved';
            $eventNote   = $data['notes'] ?? 'Request approved';
        } elseif ($decision === 'rejected') {
            $newStatus   = 'rejected';
            $eventAction = 'rejected';
            $eventNote   = $data['notes'] ?? 'Request rejected';
        }

        $isDecided = in_array($decision, ['accepted', 'rejected'], true);

        $req = DB::transaction(function () use (
            $data, $branchId, $newStatus, $eventAction, $eventNote,
            $decision, $isDecided, $payrollReference, $approverId,
            $employeeId, $payload
        ) {
            if ($data['type'] === 'monthly_salary' && !$payrollReference) {
                throw new \RuntimeException(
                    'No payroll reference was found for the selected branch and period.'
                );
            }

            $req = PayrollRequest::create([
                'branch_id'        => $branchId,
                'reference'        => $data['type'] === 'monthly_salary' ? $payrollReference : null,
                'type'             => $data['type'],
                'period_month'     => (int) $data['period_month'],
                'period_year'      => (int) $data['period_year'],

                'employee_id'      => $employeeId,       // null for bulk monthly
                'approver_id'      => $approverId,       // scalar or null

                'amount'           => $data['amount'],
                'employee_count'   => (int) $data['employee_count'],
                'status'           => $newStatus,

                'requested_by'     => Auth::id(),
                'requested_at'     => now(),

                'approved_by'      => $isDecided ? Auth::id() : null,
                'approved_at'      => $isDecided ? now() : null,

                'rejection_reason' => $decision === 'rejected'
                    ? ($data['rejection_reason'] ?? ($data['notes'] ?? 'Rejected by approver'))
                    : null,

                'notes'            => $data['notes'] ?? null,
                'payload'          => $payload,          // cast → JSON string
            ]);

            PayrollRequestEvent::create([
                'payroll_request_id' => $req->id,
                'action'             => $eventAction,
                'actor_id'           => Auth::id(),
                'notes'              => $eventNote,
            ]);

            return $req;
        });

        /*
        |------------------------------------------------------------------
        | 9. SMS when submitted for approval
        |------------------------------------------------------------------
        */
        $smsSent = false;

        if ($newStatus === 'pending' && $approverId) {
            $smsMessage =
                "NEW PAYROLL REQUEST\n" .
                "Requester: {$requesterName}\n" .
                "Message: " . ($data['notes'] ?? 'No message provided');

            $smsSent = $this->notifyPayrollApprover(
                $approverId,
                $smsMessage,
                'payroll-' . $req->id . '-new-' . now()->format('YmdHis')
            );
        }

        $message = $newStatus === 'pending'
            ? 'Request submitted for approval.'
            : ($newStatus === 'approved' ? 'Request approved.' : 'Request rejected.');

        return response()->json([
            'message'  => $message,
            'request'  => $req,
            'status'   => $newStatus,
            'sms_sent' => $smsSent,
        ]);
    }

    /**
     * Coerce a scalar or single-element array to int.
     * Prevents "Array to string conversion" when a multi-select
     * accidentally posts an array to a scalar column.
     */
    private function scalarInt($value): ?int
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }


// public function store(Request $request)
// {
//     $data = $request->validate([
//         'type'             => 'required|in:monthly_salary,salary_advance',
//         'period_month'     => 'required|integer|between:1,12',
//         'period_year'      => 'required|integer|min:2020',
//         'employee_id'      => 'required_if:type,salary_advance|nullable|exists:employees,id',
//         'amount'           => 'required|numeric|min:0.01',
//         'employee_count'   => 'required|integer|min:1',
//         'payload'          => 'nullable|array',
//         'notes'            => 'nullable|string|max:1000',
//         'decision'         => 'nullable|in:accepted,rejected',
//         'rejection_reason' => 'nullable|string|max:1000',
//     ]);

//     $branchId = Auth::user()->branch_id;
//     $decision = $data['decision'] ?? null;

//     $period = sprintf(
//         '%04d-%02d',
//         $data['period_year'],
//         $data['period_month']
//     );

//     /*
//     |--------------------------------------------------------------------------
//     | Get reference from payrolls table
//     |--------------------------------------------------------------------------
//     */

//     $payrollReference = DB::table('payrolls')
//         ->where('branch_id', $branchId)
//         ->where('month', $period)
//         ->value('reference');

//     /*
//     |--------------------------------------------------------------------------
//     | Find existing request
//     |--------------------------------------------------------------------------
//     */

//     $existing = PayrollRequest::forBranch($branchId)
//         ->where('type', $data['type'])
//         ->where('period_year', $data['period_year'])
//         ->where('period_month', $data['period_month'])
//         ->whereIn('status', ['pending', 'approved', 'rejected', 'cancelled'])
//         ->when($data['type'] === 'salary_advance', function ($q) use ($data) {
//             $q->where('employee_id', $data['employee_id']);
//         })
//         ->latest('id')
//         ->first();

//     /*
//     |--------------------------------------------------------------------------
//     | 1. APPROVED - Prevent overwrite
//     |--------------------------------------------------------------------------
//     */

//     if ($existing && $existing->status === 'approved') {
//         return response()->json([
//             'errors' => [
//                 'type' => ['A request for this period is already approved.'],
//             ],
//         ], 422);
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | 2. PENDING - Approve, reject or update
//     |--------------------------------------------------------------------------
//     */

//     if ($existing && $existing->status === 'pending') {

//         /*
//         |--------------------------------------------------------------
//         | Approve or reject
//         |--------------------------------------------------------------
//         */

//         if (in_array($decision, ['accepted', 'rejected'], true)) {

//             $newStatus = $decision === 'accepted'
//                 ? 'approved'
//                 : 'rejected';

//             $eventAction = $decision === 'accepted'
//                 ? 'approved'
//                 : 'rejected';

//             $eventNote = $data['notes']
//                 ?? ($decision === 'accepted'
//                     ? 'Request approved'
//                     : 'Request rejected');

//             DB::transaction(function () use (
//                 $existing,
//                 $data,
//                 $decision,
//                 $newStatus,
//                 $eventAction,
//                 $eventNote
//             ) {
//                 $update = [
//                     'status'      => $newStatus,
//                     'approved_by' => Auth::id(),
//                     'approved_at' => now(),
//                 ];

//                 if ($decision === 'rejected') {
//                     $update['rejection_reason'] =
//                         $data['rejection_reason']
//                         ?? ($data['notes'] ?? 'Rejected by approver');
//                 }

//                 $existing->update($update);

//                 PayrollRequestEvent::create([
//                     'payroll_request_id' => $existing->id,
//                     'action'             => $eventAction,
//                     'actor_id'           => Auth::id(),
//                     'notes'              => $eventNote,
//                 ]);
//             });

//             return response()->json([
//                 'message' => $decision === 'accepted'
//                     ? 'Request approved.'
//                     : 'Request rejected.',
//                 'request' => $existing->fresh(),
//                 'status'  => $newStatus,
//             ]);
//         }

//         /*
//         |--------------------------------------------------------------
//         | Update pending request
//         |--------------------------------------------------------------
//         */

//         DB::transaction(function () use ($existing, $data) {

//             $existing->update([
//                 'employee_id'    => $data['employee_id'] ?? null,
//                 'amount'         => $data['amount'],
//                 'employee_count' => $data['employee_count'],
//                 'notes'          => $data['notes'] ?? null,
//                 'payload'        => $data['payload'] ?? null,
//                 'requested_by'   => Auth::id(),
//                 'requested_at'   => now(),
//             ]);

//             PayrollRequestEvent::create([
//                 'payroll_request_id' => $existing->id,
//                 'action'             => 'updated',
//                 'actor_id'           => Auth::id(),
//                 'notes'              => 'Pending request updated and resubmitted for approval',
//             ]);
//         });

//         return response()->json([
//             'message' => 'Pending request updated.',
//             'request' => $existing->fresh(),
//             'updated' => true,
//         ]);
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | 3. Create new request
//     |--------------------------------------------------------------------------
//     */

//     $newStatus = 'pending';
//     $eventAction = 'created';
//     $eventNote = 'Request submitted for approval';

//     if ($decision === 'accepted') {
//         $newStatus = 'approved';
//         $eventAction = 'approved';
//         $eventNote = $data['notes'] ?? 'Request approved';

//     } elseif ($decision === 'rejected') {
//         $newStatus = 'rejected';
//         $eventAction = 'rejected';
//         $eventNote = $data['notes'] ?? 'Request rejected';
//     }

//     $isDecided = in_array($decision, ['accepted', 'rejected'], true);

//     $req = DB::transaction(function () use (
//         $data,
//         $branchId,
//         $newStatus,
//         $eventAction,
//         $eventNote,
//         $decision,
//         $isDecided,
//         $payrollReference
//     ) {

//         /*
//         |--------------------------------------------------------------
//         | Use reference from payrolls table
//         |--------------------------------------------------------------
//         */

//         if ($data['type'] === 'monthly_salary' && !$payrollReference) {
//             throw new \Exception(
//                 'No payroll reference was found for the selected branch and period.'
//             );
//         }

//         $req = PayrollRequest::create([
//             'branch_id'        => $branchId,

//             // Reference comes from payrolls table
//             'reference'        => $data['type'] === 'monthly_salary'
//                 ? $payrollReference
//                 : null,

//             'type'             => $data['type'],
//             'period_month'     => $data['period_month'],
//             'period_year'      => $data['period_year'],
//             'employee_id'      => $data['employee_id'] ?? null,
//             'amount'           => $data['amount'],
//             'employee_count'   => $data['employee_count'],
//             'status'           => $newStatus,
//             'requested_by'     => Auth::id(),
//             'requested_at'     => now(),

//             'approved_by'      => $isDecided ? Auth::id() : null,
//             'approved_at'      => $isDecided ? now() : null,

//             'rejection_reason' => $decision === 'rejected'
//                 ? ($data['rejection_reason']
//                     ?? ($data['notes'] ?? 'Rejected by approver'))
//                 : null,

//             'notes'            => $data['notes'] ?? null,
//             'payload'          => $data['payload'] ?? null,
//         ]);

//         PayrollRequestEvent::create([
//             'payroll_request_id' => $req->id,
//             'action'             => $eventAction,
//             'actor_id'           => Auth::id(),
//             'notes'              => $eventNote,
//         ]);

//         return $req;
//     });

//     $message = $newStatus === 'pending'
//         ? 'Request submitted for approval.'
//         : ($newStatus === 'approved'
//             ? 'Request approved.'
//             : 'Request rejected.');

//     return response()->json([
//         'message' => $message,
//         'request' => $req,
//         'status'  => $newStatus,
//     ]);
// }

    public function approve(Request $request, PayrollRequest $req)
    {
        $this->guardCanApprove($req);

        if ($req->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can be approved.'], 422);
        }

        DB::transaction(function () use ($req) {
            $req->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            PayrollRequestEvent::create([
                'payroll_request_id' => $req->id,
                'action'             => 'approved',
                'actor_id'           => Auth::id(),
            ]);
        });

        return response()->json(['message' => 'Request approved.']);
    }

    public function reject(Request $request, PayrollRequest $req)
    {
        $this->guardCanApprove($req);

        $data = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($req->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can be rejected.'], 422);
        }

        DB::transaction(function () use ($req, $data) {
            $req->update([
                'status'           => 'rejected',
                'rejected_by'      => Auth::id(),
                'rejected_at'      => now(),
                'rejection_reason' => $data['rejection_reason'],
            ]);
            PayrollRequestEvent::create([
                'payroll_request_id' => $req->id,
                'action'             => 'rejected',
                'actor_id'           => Auth::id(),
                'notes'              => $data['rejection_reason'],
            ]);
        });

        return response()->json(['message' => 'Request rejected.']);
    }

    public function cancel(Request $request, PayrollRequest $req)
    {
        if ($req->requested_by !== Auth::id()
            && !in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403);
        }
        if (!in_array($req->status, ['draft','pending'])) {
            return response()->json(['message' => 'This request can no longer be cancelled.'], 422);
        }

        $req->update(['status' => 'cancelled']);
        PayrollRequestEvent::create([
            'payroll_request_id' => $req->id,
            'action'             => 'cancelled',
            'actor_id'           => Auth::id(),
        ]);

        return response()->json(['message' => 'Request cancelled.']);
    }

    private function guardCanApprove(PayrollRequest $req): void
    {
        $user = Auth::user();

        if ($req->branch_id !== $user->branch_id) {
            abort(403, 'Wrong branch.');
        }
        if ($req->requested_by === $user->id) {
            abort(403, 'You cannot approve your own request.');
        }
        if (!in_array($user->role ?? '', ['admin','manager','finance_manager'])) {
            abort(403, 'You are not authorised to approve payroll requests.');
        }
    }




private function notifyPayrollApprover($approverId, $message, $reference = null): bool
{
    try {

        Log::info('PAYROLL SMS: Looking up selected approver', [
            'approver_id' => $approverId,
        ]);

        $approver = DB::table('users')
            ->where('id', $approverId)
            ->first();

        if (!$approver) {
            Log::error('PAYROLL SMS: Approver not found', [
                'approver_id' => $approverId,
            ]);

            return false;
        }

        if (empty($approver->mobile)) {
            Log::error('PAYROLL SMS: Approver phone number is missing', [
                'approver_id' => $approverId,
            ]);

            return false;
        }

        Log::info('PAYROLL SMS: Sending to selected approver', [
            'approver_id' => $approverId,
            'reference'   => $reference,
        ]);

        $sent = app(\App\Services\NextSmsService::class)->send(
            $approver->mobile,
            $message,
            $reference
        );

        Log::info('PAYROLL SMS: Sending result', [
            'approver_id' => $approverId,
            'sms_sent'    => $sent,
            'reference'   => $reference,
        ]);

        return $sent;

    } catch (\Throwable $e) {

        Log::error('PAYROLL SMS: Notification exception', [
            'approver_id' => $approverId,
            'error'       => $e->getMessage(),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
        ]);

        return false;
    }
}

public function show() {
    return 1;
}
}