<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\PettyCashCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AccountTransactionController extends Controller
{
    private function branchQuery()
    {
        return AccountTransaction::where(
            'branch_id',
            Auth::user()->branch_id
        );
    }

    public function index(Request $request)
    {
        $query = $this->branchQuery();

        if ($request->filled('startdate')) {
            $query->whereDate(
                'transaction_date',
                '>=',
                $request->startdate
            );
        }

        if ($request->filled('enddate')) {
            $query->whereDate(
                'transaction_date',
                '<=',
                $request->enddate
            );
        }

        if ($request->filled('transaction_type')) {
            $query->where(
                'transaction_type',
                $request->transaction_type
            );
        }

        if ($request->filled('expense_category_id')) {
            $query->where(
                'expense_category_id',
                $request->expense_category_id
            );
        }

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $categories = PettyCashCategory::orderBy('name')->get();

        return view(
            'accountTransactions.index',
            compact('transactions', 'categories')
        );
    }

    public function create()
    {
        $transaction = new AccountTransaction();

        $categories = PettyCashCategory::orderBy('name')->get();

        return view(
            'accountTransactions.form',
            compact('transaction', 'categories')
        );
    }

    public function store(Request $request)
    {
        $data = $this->validateTransaction($request);

        $data['branch_id'] = Auth::user()->branch_id;
        $data['created_by'] = Auth::id();

        DB::transaction(function () use ($data) {
            AccountTransaction::create($data);
        });

        return redirect()
            ->route('account-transactions.index')
            ->with('success', 'Transaction recorded successfully.');
    }

    public function edit($id)
    {
        $transaction = $this->branchQuery()->findOrFail($id);

        $categories = PettyCashCategory::orderBy('name')->get();

        return view(
            'accountTransactions.form',
            compact('transaction', 'categories')
        );
    }

    public function update(Request $request, $id)
    {
        $transaction = $this->branchQuery()->findOrFail($id);

        $data = $this->validateTransaction($request);

        DB::transaction(function () use ($transaction, $data) {
            $transaction->update($data);
        });

        return redirect()
            ->route('account-transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy($id)
    {
        $transaction = $this->branchQuery()->findOrFail($id);

        // Prevent deleting automatically posted transactions.
        if (!empty($transaction->reference_type)) {
            return redirect()
                ->route('account-transactions.index')
                ->with(
                    'error',
                    'This transaction is linked to another record and cannot be deleted here.'
                );
        }

        $transaction->delete();

        return redirect()
            ->route('account-transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    private function validateTransaction(Request $request)
    {
        $data = $request->validate([
            'transaction_date' => ['required', 'date'],

            'transaction_type' => [
                'required',
                Rule::in([
                    'opening_balance',
                    'cash_collection',
                    'expense',
                    'cash_payment',
                    'adjustment',
                ]),
            ],

            'particulars' => ['required', 'string', 'max:500'],
            'voucher_type' => ['nullable', 'string', 'max:100'],
            'voucher_no' => ['nullable', 'string', 'max:255'],
            'account_id' => ['nullable', 'integer'],
            'expense_category_id' => [
                'nullable',
                'integer',
                'exists:petty_cash_categories,id',
            ],
            'debit' => ['required', 'numeric', 'min:0', 'max:9999999999999999'],
            'credit' => ['required', 'numeric', 'min:0', 'max:9999999999999999'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $debit = (float) $data['debit'];
        $credit = (float) $data['credit'];

        if ($debit <= 0 && $credit <= 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'debit' => 'Enter a positive debit or credit amount.',
            ]);
        }

        if ($debit > 0 && $credit > 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'credit' => 'Enter either a debit or a credit, not both.',
            ]);
        }

        return $data;
    }


    
public function report(Request $request)
{
    $request->validate([
        'startdate' => ['nullable', 'date'],
        'enddate' => [
            'nullable',
            'date',
            'after_or_equal:startdate'
        ],
        'transaction_type' => [
            'nullable',
            'in:opening_balance,cash_collection,expense,cash_payment,adjustment'
        ],
        'expense_category_id' => ['nullable', 'integer'],
        'search' => ['nullable', 'string', 'max:150'],
    ]);

    $branchId = auth()->user()->branch_id;

    // Default to the current month when dates are not supplied.
    $startdate = $request->input(
        'startdate',
        now()->startOfMonth()->toDateString()
    );

    $enddate = $request->input(
        'enddate',
        now()->toDateString()
    );

    $filters = [
        'startdate' => $startdate,
        'enddate' => $enddate,
        'transaction_type' => $request->transaction_type,
        'expense_category_id' => $request->expense_category_id,
        'search' => $request->search,
    ];

    // Shared filters for the selected branch and transaction type.
    $baseQuery = \App\Models\AccountTransaction::query()
        ->where('branch_id', $branchId);

    if ($request->filled('transaction_type')) {
        $baseQuery->where(
            'transaction_type',
            $request->transaction_type
        );
    }

    if ($request->filled('expense_category_id')) {
        $baseQuery->where(
            'expense_category_id',
            $request->expense_category_id
        );
    }

    if ($request->filled('search')) {
        $search = $request->search;

        $baseQuery->where(function ($query) use ($search) {
            $query->where('particulars', 'like', "%{$search}%")
                ->orWhere('voucher_no', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Calculate the balance before the selected period.
$balanceQuery = \App\Models\AccountTransaction::query()
    ->where('branch_id', $branchId);

// Keep the category filter, but do not apply the
// transaction type or text search to the opening balance.
if ($request->filled('expense_category_id')) {
    $balanceQuery->where(
        'expense_category_id',
        $request->expense_category_id
    );
}

$openingBalance = (float) (
    (clone $balanceQuery)
        ->whereDate('transaction_date', '<', $startdate)
        ->sum('debit')
    -
    (clone $balanceQuery)
        ->whereDate('transaction_date', '<', $startdate)
        ->sum('credit')
);

    // Transactions within the selected period.
    $periodQuery = clone $baseQuery;

    $periodQuery->whereDate('transaction_date', '>=', $startdate)
        ->whereDate('transaction_date', '<=', $enddate);

    $totalDebit = (float) (clone $periodQuery)->sum('debit');
    $totalCredit = (float) (clone $periodQuery)->sum('credit');

    $transactions = (clone $periodQuery)
        ->orderBy('transaction_date')
        ->orderBy('id')
        ->get();

    // Running balance for each ledger entry.
    $runningBalance = $openingBalance;

    foreach ($transactions as $transaction) {
        $runningBalance +=
            (float) $transaction->debit -
            (float) $transaction->credit;

        $transaction->running_balance = $runningBalance;
    }

    $closingBalance = $runningBalance;

    $categories = \App\Models\PettyCashCategory::orderBy('name')
        ->get();

    return view(
        'accountTransactions.report',
        compact(
            'transactions',
            'categories',
            'filters',
            'openingBalance',
            'totalDebit',
            'totalCredit',
            'closingBalance'
        )
    );
}

}
