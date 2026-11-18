<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SmsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD / INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        try {
            $senderIds = DB::table('sender_ids')
                ->orderByDesc('id')
                ->get();

            $branches = DB::table('branches')
                ->orderBy('branch_name')
                ->get();

            $employees = DB::table('employees')
                ->whereNotNull('mobile')
                ->where('mobile', '<>', '')
                ->orderBy('firstname')
                ->get();

            // Your customer table is clients, not customers.
            $customers = DB::table('clients')
                ->whereNotNull('phone')
                ->where('phone', '<>', '')
                ->orderBy('client_name')
                ->get();

            $logs = DB::table('sms_logs')
                ->leftJoin(
                    'sender_ids',
                    'sender_ids.id',
                    '=',
                    'sms_logs.sender_id'
                )
                ->select(
                    'sms_logs.*',
                    'sender_ids.name as sender_name'
                )
                ->orderByDesc('sms_logs.id')
                ->limit(100)
                ->get();

            return view('sms.index', compact(
                'senderIds',
                'branches',
                'employees',
                'customers',
                'logs'
            ));

        } catch (\Throwable $e) {

            $this->logSmsError('SMS DASHBOARD ERROR', $e);

            return back()->withErrors(
                'Unable to load SMS dashboard. Please check the application logs.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE SENDER ID
    |--------------------------------------------------------------------------
    */

    public function storeSenderId(Request $request)
    {
        $request->validate([
            'sender_id' => [
                'required',
                'string',
                'max:11',
                'regex:/^[A-Za-z0-9 ]+$/',
                'unique:sender_ids,name'
            ]
        ], [
            'sender_id.required' => 'Please enter a Sender ID.',
            'sender_id.max' => 'Sender ID cannot exceed 11 characters.',
            'sender_id.unique' => 'This Sender ID already exists.',
            'sender_id.regex' => 'Sender ID can contain letters, numbers and spaces only.'
        ]);

        try {
            DB::table('sender_ids')->insert([
                'name' => strtoupper(trim($request->sender_id)),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return back()->with(
                'success',
                'Sender ID submitted successfully for approval.'
            );

        } catch (\Throwable $e) {

            $this->logSmsError('CREATE SENDER ID ERROR', $e);

            return back()
                ->withInput()
                ->withErrors('Unable to save Sender ID. Please check the logs.');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE SENDER ID
    |--------------------------------------------------------------------------
    */

    public function updateSenderId(Request $request, $id)
    {
        $sender = DB::table('sender_ids')->find($id);

        if (!$sender) {
            return back()->withErrors('Sender ID not found.');
        }

        $request->validate([
            'sender_id' => [
                'required',
                'string',
                'max:11',
                'regex:/^[A-Za-z0-9 ]+$/',
                Rule::unique('sender_ids', 'name')->ignore($id)
            ],
            'status' => 'required|in:pending,approved,rejected'
        ]);

        try {
            DB::table('sender_ids')
                ->where('id', $id)
                ->update([
                    'name' => strtoupper(trim($request->sender_id)),
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            return back()->with(
                'success',
                'Sender ID updated successfully.'
            );

        } catch (\Throwable $e) {

            $this->logSmsError('UPDATE SENDER ID ERROR', $e);

            return back()
                ->withInput()
                ->withErrors('Unable to update Sender ID.');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE SENDER ID
    |--------------------------------------------------------------------------
    */

    public function deleteSenderId($id)
    {
        try {
            $sender = DB::table('sender_ids')->find($id);

            if (!$sender) {
                return back()->withErrors('Sender ID not found.');
            }

            $used = DB::table('sms_logs')
                ->where('sender_id', $id)
                ->exists();

            if ($used) {
                return back()->withErrors(
                    'This Sender ID has SMS history and cannot be deleted.'
                );
            }

            DB::table('sender_ids')
                ->where('id', $id)
                ->delete();

            return back()->with(
                'success',
                'Sender ID deleted successfully.'
            );

        } catch (\Throwable $e) {

            $this->logSmsError('DELETE SENDER ID ERROR', $e);

            return back()->withErrors(
                'Unable to delete Sender ID.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SEND SMS
    |--------------------------------------------------------------------------
    */

    public function sendSms(Request $request)
    {
        // Validate before the main try/catch so Laravel can display
        // normal validation errors in the Blade view.
        $request->validate([
            'sender_id' => 'required|exists:sender_ids,id',

            'recipient_types' => 'nullable|array|min:1',
            'recipient_types.*' => 'nullable|in:staff,clients,custom',

            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'integer',

            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'integer',

            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'integer',

            'custom_recipients' => 'nullable|string',

            'message' => 'required|string|max:1000',

            'channel' => 'required|in:sms,whatsapp'
        ]);

        try {

            /*
            |--------------------------------------------------------------------------
            | CHECK SENDER ID
            |--------------------------------------------------------------------------
            */

            $sender = DB::table('sender_ids')
                ->where('id', $request->sender_id)
                ->where('status', 'approved')
                ->first();

            if (!$sender) {
                return back()
                    ->withInput()
                    ->withErrors(
                        'Selected Sender ID is not approved.'
                    );
            }

            $recipientTypes = array_unique(
                $request->input('recipient_types', [])
            );

            $numbers = [];


            /*
            |--------------------------------------------------------------------------
            | STAFF RECIPIENTS
            |--------------------------------------------------------------------------
            */

            if (in_array('staff', $recipientTypes)) {

                $branchIds = array_unique(
                    $request->input('branch_ids', [])
                );

                $employeeIds = array_unique(
                    $request->input('employee_ids', [])
                );

                if (empty($branchIds)) {
                    return back()
                        ->withInput()
                        ->withErrors(
                            'Please select at least one staff branch.'
                        );
                }

                if (empty($employeeIds)) {
                    return back()
                        ->withInput()
                        ->withErrors(
                            'Please select at least one staff member.'
                        );
                }

                // Confirm selected branches exist.
                $validBranches = DB::table('branches')
                    ->whereIn('id', $branchIds)
                    ->pluck('id')
                    ->toArray();

                if (count($validBranches) !== count($branchIds)) {
                    return back()
                        ->withInput()
                        ->withErrors('Invalid branch selection.');
                }

                // Get employees belonging to the selected branches
                // and authenticated business.
                $staffQuery = DB::table('employees')
                    ->whereIn('branch_id', $validBranches)
                    ->whereIn('id', $employeeIds);

                $validEmployeeCount = (clone $staffQuery)->count();

                if ($validEmployeeCount !== count($employeeIds)) {
                    return back()
                        ->withInput()
                        ->withErrors('Invalid staff selection.');
                }

                $staff = $staffQuery
                    ->whereNotNull('mobile')
                    ->where('mobile', '<>', '')
                    ->pluck('mobile')
                    ->toArray();

                $numbers = array_merge($numbers, $staff);
            }


            /*
            |--------------------------------------------------------------------------
            | CLIENT / CUSTOMER RECIPIENTS
            |--------------------------------------------------------------------------
            */

            if (in_array('clients', $recipientTypes)) {

                $customerIds = array_unique(
                    $request->input('customer_ids', [])
                );

                if (empty($customerIds)) {
                    return back()
                        ->withInput()
                        ->withErrors(
                            'Please select at least one customer.'
                        );
                }

                // Your uploaded controller uses the clients table.
                $customerCount = DB::table('clients')
                    ->whereIn('id', $customerIds)
                    ->count();

                if ($customerCount !== count($customerIds)) {
                    return back()
                        ->withInput()
                        ->withErrors('Invalid customer selection.');
                }

                $customers = DB::table('clients')
                    ->whereIn('id', $customerIds)
                    ->whereNotNull('phone')
                    ->where('phone', '<>', '')
                    ->pluck('phone')
                    ->toArray();

                $numbers = array_merge($numbers, $customers);
            }


            /*
            |--------------------------------------------------------------------------
            | CUSTOM RECIPIENTS
            |--------------------------------------------------------------------------
            */

            if (in_array('custom', $recipientTypes)) {

                $customNumbers = preg_split(
                    '/[\s,;]+/',
                    trim($request->custom_recipients ?? ''),
                    -1,
                    PREG_SPLIT_NO_EMPTY
                );

                if (empty($customNumbers)) {
                    return back()
                        ->withInput()
                        ->withErrors(
                            'Please enter at least one custom mobile number.'
                        );
                }

                $numbers = array_merge($numbers, $customNumbers);
            }


            /*
            |--------------------------------------------------------------------------
            | NORMALIZE MOBILE NUMBERS
            |--------------------------------------------------------------------------
            */

            $numbers = $this->normalizeMobileNumbers($numbers);

            if (empty($numbers['valid'])) {

                return back()
                    ->withInput()
                    ->withErrors(
                        'No valid mobile numbers were found.'
                    );
            }

            if (!empty($numbers['invalid'])) {

                return back()
                    ->withInput()
                    ->withErrors(
                        'Invalid mobile number(s): ' .
                        implode(', ', array_slice($numbers['invalid'], 0, 5))
                    );
            }

            $validNumbers = $numbers['valid'];


            /*
            |--------------------------------------------------------------------------
            | WHATSAPP CHECK
            |--------------------------------------------------------------------------
            */

            if ($request->channel === 'whatsapp') {

                return back()
                    ->withInput()
                    ->withErrors(
                        'WhatsApp integration is not configured yet. Please select SMS.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | PREPARE RECIPIENTS
            |--------------------------------------------------------------------------
            */

            $recipients = [];

            foreach ($validNumbers as $mobile) {

                $recipients[] = [
                    'mobile' => $mobile,
                    'message' => $request->message
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | SEND THROUGH NEXT SMS
            |--------------------------------------------------------------------------
            */

            $response = $this->sendViaNextSms(
                $sender->name,
                $recipients
            );


            /*
            |--------------------------------------------------------------------------
            | SAVE SMS LOGS
            |--------------------------------------------------------------------------
            */

            $logs = [];

            foreach ($recipients as $row) {

                $logs[] = [
                    'sender_id' => $sender->id,
                    'recipient' => $row['mobile'],
                    'message' => $row['message'],
                    'channel' => 'sms',
                    'status' => $response['status'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            foreach (array_chunk($logs, 500) as $chunk) {
                DB::table('sms_logs')->insert($chunk);
            }


            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            if ($response['status'] === 'sent') {

                return back()->with(
                    'success',
                    count($validNumbers) .
                    ' SMS message(s) submitted successfully.'
                );
            }

            return back()
                ->withInput()
                ->withErrors(
                    'SMS request failed. Provider response: ' .
                    ($response['response'] ?? 'No response received.')
                );

        } catch (\Throwable $e) {

            $this->logSmsError('SMS SEND ERROR', $e, [
                'user_id' => Auth::id(),
                'business_id' => Auth::user()->business_id ?? null,
                'sender_id' => $request->sender_id,
                'channel' => $request->channel,
                'recipient_types' => $request->recipient_types,
                'recipient_count' => count(
                    $request->input('employee_ids', [])
                ) + count(
                    $request->input('customer_ids', [])
                )
            ]);

            return back()
                ->withInput()
                ->withErrors(
                    'SMS sending error: ' . $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE MOBILE NUMBERS
    |--------------------------------------------------------------------------
    */

private function normalizeMobileNumbers(array $numbers)
{
    $valid = [];
    $invalid = [];

    foreach ($numbers as $number) {

        $number = trim((string) $number);

        // Remove spaces, hyphens, brackets and other separators.
        $number = preg_replace('/[\s\-().]+/', '', $number);

        // Remove leading +
        if (str_starts_with($number, '+')) {
            $number = substr($number, 1);
        }

        // Convert local format: 0686382143 => 255686382143
        if (preg_match('/^0[67]\d{8}$/', $number)) {
            $number = '255' . substr($number, 1);
        }

        // Convert local format: 0756382143 => 255756382143
        // Validate Tanzanian mobile format.
        if (!preg_match('/^255[67]\d{8}$/', $number)) {
            $invalid[] = $number;
            continue;
        }

        $valid[] = $number;
    }

    return [
        'valid'   => array_values(array_unique($valid)),
        'invalid' => array_values(array_unique($invalid))
    ];
}


    /*
    |--------------------------------------------------------------------------
    | NEXT SMS BULK API
    |--------------------------------------------------------------------------
    */

    private function sendViaNextSms($senderId, array $recipients)
    {
        $apiUrl = config('services.nextsms.url');
        $apiKey = config('services.nextsms.key');
        $apiSecret = config('services.nextsms.secret');

        /*
        |--------------------------------------------------------------------------
        | CHECK CONFIGURATION
        |--------------------------------------------------------------------------
        */

        if (empty($apiUrl) || empty($apiKey) || empty($apiSecret)) {

            Log::error('NEXT SMS CONFIGURATION ERROR', [
                'url_configured' => !empty($apiUrl),
                'key_configured' => !empty($apiKey),
                'secret_configured' => !empty($apiSecret)
            ]);

            return [
                'status' => 'failed',
                'response' => 'NextSMS configuration is missing.'
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | PREPARE API PAYLOAD
        |--------------------------------------------------------------------------
        */

        $messages = [];

        foreach ($recipients as $recipient) {

            $messages[] = [
                'from' => $senderId,
                'to' => $recipient['mobile'],
                'text' => $recipient['message']
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | SEND REQUEST
        |--------------------------------------------------------------------------
        */

        try {

            Log::info('NEXT SMS REQUEST STARTED', [
                'sender_id' => $senderId,
                'recipient_count' => count($messages),
                'url' => $apiUrl
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(
                    $apiKey . ':' . $apiSecret
                ),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
                ->timeout(120)
                ->post(rtrim(trim($apiUrl), '/'), [
                    'messages' => $messages
                ]);


            /*
            |--------------------------------------------------------------------------
            | LOG PROVIDER RESPONSE
            |--------------------------------------------------------------------------
            */

            Log::info('NEXT SMS RESPONSE', [
                'http_status' => $response->status(),
                'successful' => $response->successful(),
                'response' => $response->body(),
                'recipient_count' => count($messages)
            ]);


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            if ($response->successful()) {

                return [
                    'status' => 'sent',
                    'response' => $response->body()
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | API FAILURE
            |--------------------------------------------------------------------------
            */

            Log::error('NEXT SMS API FAILED', [
                'http_status' => $response->status(),
                'response' => $response->body(),
                'recipient_count' => count($messages)
            ]);

            return [
                'status' => 'failed',
                'response' => $response->body()
            ];

        } catch (\Throwable $e) {

            $this->logSmsError('NEXT SMS EXCEPTION', $e, [
                'sender_id' => $senderId,
                'recipient_count' => count($recipients)
            ]);

            return [
                'status' => 'failed',
                'response' => $e->getMessage()
            ];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SEND SMS FROM INVOICE
    |--------------------------------------------------------------------------
    */

    public function sendInvoiceSms($invoiceId)
    {
        try {

            $invoice = DB::table('tax_invoices')
                ->find($invoiceId);

            if (!$invoice) {
                return back()->withErrors('Invoice not found.');
            }

            $sender = DB::table('sender_ids')
                ->where('status', 'approved')
                ->orderBy('id')
                ->first();

            if (!$sender) {
                return back()->withErrors(
                    'No approved Sender ID available.'
                );
            }

            $mobile = $invoice->customer_phone ?? null;

            if (!$mobile) {
                return back()->withErrors(
                    'Customer mobile number not found.'
                );
            }

            $message = "Invoice #{$invoice->invoice_no}, Amount: "
                . number_format($invoice->total, 2)
                . ". Thank you for your business.";

            $normalized = $this->normalizeMobileNumbers([$mobile]);

            if (empty($normalized['valid']) || !empty($normalized['invalid'])) {
                return back()->withErrors(
                    'Invalid customer mobile number.'
                );
            }

            $mobile = $normalized['valid'][0];

            $response = $this->sendViaNextSms($sender->name, [[
                'mobile' => $mobile,
                'message' => $message
            ]]);


            /*
            |--------------------------------------------------------------------------
            | SAVE INVOICE SMS LOG
            |--------------------------------------------------------------------------
            */

            DB::table('sms_logs')->insert([
                'sender_id' => $sender->id,
                'recipient' => $mobile,
                'message' => $message,
                'channel' => 'sms',
                'status' => $response['status'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($response['status'] !== 'sent') {

                Log::error('INVOICE SMS FAILED', [
                    'invoice_id' => $invoiceId,
                    'mobile' => $mobile,
                    'response' => $response['response']
                ]);

                return back()->withErrors(
                    'Invoice SMS could not be submitted. ' .
                    ($response['response'] ?? '')
                );
            }

            return back()->with(
                'success',
                'Invoice SMS submitted successfully.'
            );

        } catch (\Throwable $e) {

            $this->logSmsError('INVOICE SMS ERROR', $e, [
                'invoice_id' => $invoiceId
            ]);

            return back()->withErrors(
                'Invoice SMS error: ' . $e->getMessage()
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SMS REPORT
    |--------------------------------------------------------------------------
    */

    public function report(Request $request)
    {
        try {

            $request->validate([
                'channel' => 'nullable|in:sms,whatsapp',
                'date' => 'nullable|date'
            ]);

            $logs = DB::table('sms_logs')
                ->leftJoin(
                    'sender_ids',
                    'sender_ids.id',
                    '=',
                    'sms_logs.sender_id'
                )
                ->select(
                    'sms_logs.*',
                    'sender_ids.name as sender_name'
                )
                ->when($request->channel, function ($query) use ($request) {
                    $query->where(
                        'sms_logs.channel',
                        $request->channel
                    );
                })
                ->when($request->date, function ($query) use ($request) {
                    $query->whereDate(
                        'sms_logs.created_at',
                        $request->date
                    );
                })
                ->orderByDesc('sms_logs.id')
                ->paginate(20)
                ->withQueryString();

            return view('sms.report', compact('logs'));

        } catch (\Throwable $e) {

            $this->logSmsError('SMS REPORT ERROR', $e);

            return back()->withErrors(
                'Unable to load SMS report.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CENTRAL ERROR LOGGER
    |--------------------------------------------------------------------------
    */

    private function logSmsError($title, \Throwable $e, array $context = [])
    {
        Log::error($title, array_merge($context, [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'error_trace' => $e->getTraceAsString(),
            'user_id' => Auth::id(),
            'created_at' => now()->toDateTimeString()
        ]));
    }
}
