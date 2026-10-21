<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtCategory;
use App\Models\DebtPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DebtController extends Controller
{
    /**
     * Debtors list + filters + summary cards.
     * Defaults to the current year and the logged-in user's branch.
     */
    public function index(Request $request)
    {
        $settings = DB::table('general_settings')
            ->select('business_name', 'logo_file', 'type', 'address')
            ->get();

        $categories = DebtCategory::orderBy('name')->get();

        $filters = $request->only(['category_id', 'status', 'month', 'year', 'search']);

        // Default to current year unless the filter form was submitted
        if (!$request->has('year')) {
            $filters['year'] = now()->year;
        }

        $debts = Debt::with(['category', 'user'])   // eager-load recorder
            ->withSum('payments', 'amount')
            ->filter($filters)
            ->where('debts.branch_id', Auth::user()->branch_id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->get();

        $summary = [
            'total'  => $debts->sum('amount'),
            'paid'   => $debts->sum(fn ($d) => $d->paid_amount),
            'unpaid' => $debts->sum(fn ($d) => $d->unpaid_amount),
        ];

        $years = collect(range(now()->year + 1, 2023))
            ->merge(Debt::distinct()->pluck('year'))
            ->unique()
            ->sortDesc()
            ->values();

        return view('debts.index', compact('debts', 'categories', 'summary', 'years', 'settings', 'filters'));
    }

    /**
     * Store a new debt — auto-assigns branch_id and user_id.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:debt_categories,id',
            'particulars' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'month'       => 'required|integer|between:1,12',
            'year'        => 'required|integer|between:2000,2100',
        ]);

        $data['created_by'] = Auth::id();
        $data['user_id']    = Auth::id();
        $data['branch_id']  = Auth::user()->branch_id;

        Debt::create($data);

        return back()->with('success', 'Debt recorded successfully.');
    }

    /**
     * Record a payment against a debt (partial or full).
     */
    public function pay(Request $request, Debt $debt)
    {
        $data = $request->validate([
            'amount'    => 'required|numeric|min:0.01',
            'paid_date' => 'required|date',
            'notes'     => 'nullable|string|max:255',
        ]);

        $balance = $debt->unpaid_amount;

        if ($data['amount'] > $balance + 0.001) {
            return back()->withErrors([
                'amount' => 'Payment exceeds the outstanding balance of ' . number_format($balance, 2),
            ])->withInput();
        }

        $data['user_id']   = Auth::id();
        $data['branch_id'] = Auth::user()->branch_id;

        DB::transaction(fn () => $debt->payments()->create($data));

        return back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Create a new debt category (AJAX from the Add Debt modal).
     */
    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:debt_categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        $category = DebtCategory::create($data);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'category' => $category]);
        }

        return back()->with('success', 'Category added successfully.');
    }

    /**
     * Statement for a single debt.
     */
    public function statement(Debt $debt)
    {
        $debt->load(['category', 'payments' => fn ($q) => $q->orderBy('paid_date')->orderBy('id')]);

        return view('debts.statement', compact('debt'));
    }

    /**
     * Statement for all debts (respects current filters + branch).
     */
    public function statementAll(Request $request)
    {
        $filters = $request->only(['category_id', 'status', 'month', 'year', 'search']);

        $debts = Debt::with('category')
            ->withSum('payments', 'amount')
            ->filter($filters)
            ->where('debts.branch_id', Auth::user()->branch_id)
            ->orderBy('year')
            ->orderBy('month')
            ->orderBy('id')
            ->get();

        $summary = [
            'total'  => $debts->sum('amount'),
            'paid'   => $debts->sum(fn ($d) => $d->paid_amount),
            'unpaid' => $debts->sum(fn ($d) => $d->unpaid_amount),
        ];

        $category = !empty($filters['category_id'])
            ? DebtCategory::find($filters['category_id'])
            : null;

        return view('debts.statement_all', compact('debts', 'summary', 'filters', 'category'));
    }

    /**
     * Stream a PDF of a single debt statement in the browser's PDF viewer.
     */
    public function statementPdf(Debt $debt)
    {
        $debt->load(['category', 'payments' => fn ($q) => $q->orderBy('paid_date')->orderBy('id')]);

        $pdf = Pdf::loadView('debts.statement', compact('debt'))
            ->setPaper('a4', 'portrait')
            ->setOption(['isRemoteEnabled' => false]);

        return $pdf->stream('statement-' . $debt->id . '.pdf');
    }

    /**
     * Download the filtered debtors report as a PDF, scoped by branch.
     */
    
public function statementAllPdf(Request $request)
{
    $filters = $request->only([
        'category_id',
        'status',
        'month',
        'year',
        'search'
    ]);

    // Business settings
    $settingsRow = DB::table('general_settings')
        ->select('business_name', 'logo_file', 'type', 'address')
        ->first();

    $settings = $settingsRow ? (array) $settingsRow : [];

    // Fetch debts using existing filters and branch restriction
    $debts = Debt::with('category')
        ->withSum('payments', 'amount')
        ->filter($filters)
        ->where('debts.branch_id', Auth::user()->branch_id)
        ->orderBy('category_id')
        ->orderBy('year')
        ->orderBy('month')
        ->orderBy('id')
        ->get();

    // Group records by category
    $groupedDebts = $debts->groupBy(function ($debt) {
        return $debt->category_id ?? 'uncategorized';
    });

    // Financial summary
    $summary = [
        'total'  => $debts->sum('amount'),
        'paid'   => $debts->sum('paid_amount'),
        'unpaid' => $debts->sum('unpaid_amount'),
    ];

    $category = !empty($filters['category_id'])
        ? DebtCategory::find($filters['category_id'])
        : null;

    $pdf = Pdf::loadView(
        'debts.statement_all',
        compact(
            'debts',
            'groupedDebts',
            'summary',
            'filters',
            'category',
            'settings'
        )
    )->setPaper('A4', 'portrait');

    $filename = 'debtors-statement-' . now()->format('Ymd-His') . '.pdf';

    return $pdf->download($filename);
}

    public function updatePayment(Request $request, DebtPayment $payment)
    {
        $data = $request->validate([
            'amount'    => 'required|numeric|min:0.01',
            'paid_date' => 'required|date',
            'notes'     => 'nullable|string|max:255',
        ]);

        $totalOther = $payment->debt->payments()
            ->where('id', '!=', $payment->id)
            ->sum('amount');

        if (($totalOther + $data['amount']) > $payment->debt->amount + 0.001) {
            return back()->withErrors([
                'amount' => 'Total payments would exceed the debt amount of '
                    . number_format($payment->debt->amount, 2),
            ])->withInput();
        }

        $payment->update($data);

        return back()->with('message', 'Payment updated successfully.');
    }

    public function destroyPayment(DebtPayment $payment)
    {
        $payment->delete();
        return back()->with('message', 'Payment deleted successfully.');
    }

    public function destroy(Debt $debt)
{
    try {
        DB::beginTransaction();
        // Delete related payments first
        $debt->payments()->delete();
        $debt->delete();
        DB::commit();

        return redirect()->route('debts.index')->with('message', 'Debt deleted successfully.');
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Failed to delete debt: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Unable to delete debt. Please try again.');
    }
}
}