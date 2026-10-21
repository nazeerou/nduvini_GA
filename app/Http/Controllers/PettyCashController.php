<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use DB;
use Pdf;
use App\Models\AccountStatement;
use App\Models\PettyCashCategory;
use App\Models\AccountHistory;
use App\Models\Account;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;




class PettyCashController extends Controller
{
    public function index()
    {
      
               $accounts = DB::table('accounts')->where('branch_id', Auth::user()->branch_id)->get();
            
                $expenses = DB::table('expenses')
                            ->select([DB::raw("SUM(amount) as total_amount"), 'created_date'])
                            ->where('branch_id', Auth::user()->branch_id)
                            ->groupBy('created_date')
                            ->orderBy('created_date', 'desc')
                            ->get();

                            $categories = DB::table('petty_cash_categories')
                            ->select('*')
                            ->orderBy('name', 'asc')
                            ->get();

            return view('expenses.index', compact('expenses', 'accounts', 'categories'));
        }
    
      public function saveExpenditure(Request $request)
    {

        $data = $request->all();        
        $data['reference_no'] = date("Ymd").date("is");
        $data['user_id'] = Auth::user()->id;

        $account_balance = Account::where('id', $request->account_id)->where('branch_id', $request->branch_id)->first();
        
        $balance =  $account_balance['total_balance'] - $request->amount;
        $name = $account_balance['name'];

        $accounting = DB::table('accounts')
                    ->where('id', $request->account_id)
                    ->where('branch_id', $request->branch_id)
                    ->update(["total_balance" => $balance]);

        Expense::create([
            'branch_id' => Auth::user()->branch_id,
            'account_id', $request->account_id,
            'expense_category_id' => $request->petty_category_id,
            'note' => $request->note,
            'amount' => $request->amount,
            'paid_to' => $request->paid_to,
            'voucher_no' => $request->voucher_no,
            'created_date' => $request->created_date
        ]);

        $account_statement = AccountStatement::create([
            'branch_id' => Auth::user()->branch_id,
            'id', $request->account_id,
            'reference' => date('Yhis'), 
            'name' => $name,
            'credit' => '',
            'debit' => $request->amount,
            'initial_balance' => $account_balance['total_balance'],
            'balance' => $balance,
            'created_at' => $request->created_date
        ]);

        return redirect('accounts/expenditures/petty-cash')->with('message', 'Expenditure added successfully');
    }

  public function addPettyCash(Request $request)
{
    $validated = $request->validate([
        'account_id'   => 'required|integer|exists:accounts,id',
        'created_date' => 'required|date_format:Y-m-d',
        'voucher_no'   => 'nullable|string|max:100',

        'expenses' => 'required|array|min:1',
        'expenses.*.expense_category_id' =>
            'required|integer|exists:petty_cash_categories,id',
        'expenses.*.amount' =>
            'required|numeric|gt:0|max:9999999999999999.99',
        'expenses.*.note'    => 'nullable|string|max:255',
        'expenses.*.paid_to' => 'nullable|string|max:191',
    ]);

    $user = Auth::user();

    if (! $user) {
        abort(401, 'Please log in to continue.');
    }

    try {
        DB::transaction(function () use ($validated, $user) {

            // Lock account to prevent simultaneous balance updates.
            $account = DB::table('accounts')
                ->where('id', $validated['account_id'])
                ->lockForUpdate()
                ->first();

            if (! $account) {
                $this->failValidation('account_id', 'The selected account was not found.');
            }

            if ((int) $account->is_active !== 1) {
                $this->failValidation('account_id', 'The selected account is inactive.');
            }

            if (
                $account->branch_id !== null &&
                (int) $account->branch_id !== (int) $user->branch_id
            ) {
                $this->failValidation('account_id', 'You cannot record expenses for this account.');
            }

            // Total the rows.
            $totalAmount = 0;
            foreach ($validated['expenses'] as $expense) {
                $totalAmount += round((float) $expense['amount'], 2);
            }
            $totalAmount = round($totalAmount, 2);

            $currentBalance = (float) (
                $account->total_balance !== null && $account->total_balance !== ''
                    ? $account->total_balance
                    : ($account->initial_balance ?? 0)
            );

            if ($totalAmount > $currentBalance) {
                $this->failValidation(
                    'expenses',
                    'Insufficient account balance. ' .
                    'Available balance: ' . number_format($currentBalance, 2) .
                    '. Total expenses: ' . number_format($totalAmount, 2) . '.'
                );
            }

            foreach ($validated['expenses'] as $expense) {
                $amount    = round((float) $expense['amount'], 2);
                $reference = now()->format('YmdHis');

                DB::table('expenses')->insert([
                    'branch_id'           => $user->branch_id,
                    'user_id'             => $user->id,
                    'expense_category_id' => $expense['expense_category_id'],
                    'amount'              => number_format($amount, 2, '.', ''),
                    'note'                => $expense['note']    ?? null,
                    'paid_to'             => $expense['paid_to'] ?? null,
                    'voucher_no'          => $validated['voucher_no'] ?? null,
                    'created_date'        => $validated['created_date'],
                    'account_id'          => $account->id,
                    'reference'           => $reference,
                ]);

                DB::table('account_histories')->insert([
                    'branch_id'    => $user->branch_id,
                    'user_id'      => $user->id,
                    'account_id'   => $account->id,
                    'amount'       => number_format(-$amount, 2, '.', ''),
                    'reference'    => $reference,
                    'note'         => 'Petty cash expense: ' . ($expense['note'] ?? 'Expense')
                                     . (! empty($validated['voucher_no'])
                                         ? ' | Voucher: ' . $validated['voucher_no']
                                         : ''),
                    'created_date' => $validated['created_date'],
                ]);
            }

            DB::update(
                "UPDATE accounts
                 SET total_balance =
                     CAST(
                         COALESCE(
                             NULLIF(total_balance, ''),
                             initial_balance,
                             0
                         ) AS DECIMAL(20,2)
                     ) - ?,
                     updated_at = NOW()
                 WHERE id = ?",
                [
                    number_format($totalAmount, 2, '.', ''),
                    $account->id,
                ]
            );
        }, 3);

        return redirect()
            ->back()
            ->with('message', 'Petty cash expenses saved successfully.');

    } catch (ValidationException $e) {
        throw $e;

    } catch (\Throwable $e) {
        Log::error('Bulk petty cash expense failed', [
            'user_id'    => $user->id,
            'account_id' => $validated['account_id'] ?? null,
            'error'      => $e->getMessage(),
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Unable to save petty cash expenses. Please try again.');
    }
}

/**
 * Flash a message to BOTH channels (session('error') and the $errors bag)
 * so any view style can display it without changing the controller logic.
 */
protected function failValidation(string $field, string $message): void
{
    session()->flash('error', $message);
    session()->flash('error_field', $field);

    throw ValidationException::withMessages([$field => $message]);
}

  public function getExpenditureDetails($date) {

        $accounts = DB::table('accounts')->select('*')->get();


        $expenses = DB::table('expenses')
        ->select('expenses.id', 'expenses.amount', 'expenses.voucher_no', 'petty_cash_categories.name', 'expenses.paid_to', 'expenses.note', 'expenses.created_date', 'users.fname', 'users.lname')
        ->leftjoin('users', 'users.id', 'expenses.user_id')
        ->leftjoin('petty_cash_categories', 'petty_cash_categories.id', 'expenses.expense_category_id')
        ->where('expenses.branch_id', Auth::user()->branch_id)
        ->where('expenses.created_date', $date)
        ->orderBy('expenses.created_date', 'desc')
        ->get();

        return view('expenses.expenses-details', compact('expenses', 'accounts'));

    }

    public function deleteExpenseByDate($date) {

        $expenses = Expense::where('created_date', $date);

        $expenses->delete();

        return redirect()->back()->with('error', 'Expenses Deleted');
    }

    public function deleteExpenseByID($id) {

        $expenses = Expense::where('id', $id)->first();

        $expenses->delete();

        return redirect()->back()->with('error', 'Expenses Deleted');
    }


    public function editExpenditure($id) {

       $expenditure = Expense::where('id', $id)->first();

       return $expenditure;

    }

    public function updateExpenditure(Request $request) {


        $accounts = DB::table('expenses')
          ->where('id', $request->id)
          ->where('branch_id', Auth::user()->branch_id)
          ->update(["voucher_no" => $request->voucher_no, "paid_to" => $request->paid_to,
          "amount" => $request->amount, "note" => $request->note, "created_date" => $request->created_date]);
        
          return redirect()->back()->with('message', 'Expenditure updated successful');
    }


    public function getAccountStatement()
    {
       $statements = AccountStatement::where('branch_id', Auth::user()->branch_id)->get();

        return view('accounts.account_statements', compact('statements'));

    }

    public function edit($id)
    {
        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('expenses-edit')) {
            $lims_expense_data = Expense::find($id);
            return $lims_expense_data;
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $lims_expense_data = Expense::find($data['expense_id']);
        $lims_expense_data->update($data);
        return redirect('expenses')->with('message', 'Data updated successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $expense_id = $request['expenseIdArray'];
        foreach ($expense_id as $id) {
            $lims_expense_data = Expense::find($id);
            $lims_expense_data->delete();
        }
        return 'Expense deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_expense_data = Expense::find($id);
        $lims_expense_data->delete();
        return redirect('expenses')->with('not_permitted', 'Data deleted successfully');
    }

    public function getPettyCashSettings() {

        $petty_cash_settings = DB::table('petty_cash_categories')->select('*')->get();

        return view('expenses.petty-cash-settings', compact('petty_cash_settings'));
    }

    public function savePettyCashSettings(Request $request) {

        $petty_cash_settings = PettyCashCategory::create([
            'name' => $request->name
        ]);
        
        return redirect('accounts/petty-cash/settings')->with('message', 'Petty cash setting Saved successfully');
    }

    public function deletePettyCashSettings($id) {

        $petty_cash = PettyCashCategory::find($id);
        $petty_cash->delete();
        return redirect()->back()->with('error', 'Petty Cash settings deleted successfully');
    }

    

public function addCashCollection(Request $request)
{
    $validated = $request->validate([
        'account_id'   => 'required|integer|exists:accounts,id',
        'amount'       => 'required|numeric|gt:0|max:9999999999999999.99',
        'note'         => 'nullable|string|max:255',
        'created_date' => 'required|date_format:Y-m-d',
    ]);

    $user = Auth::user();

    if (!$user) {
        abort(401, 'Please log in to continue.');
    }

    try {
        DB::transaction(function () use ($validated, $user) {

            /*
             * 1. Lock the selected account before updating its balance.
             */
            $account = DB::table('accounts')
                ->where('id', $validated['account_id'])
                ->lockForUpdate()
                ->first();

            if (!$account) {
                throw ValidationException::withMessages([
                    'account_id' => 'The selected account does not exist.',
                ]);
            }

            /*
             * 2. Check account status.
             * The accounts table contains an is_active column.
             */
            if ((int) $account->is_active !== 1) {
                throw ValidationException::withMessages([
                    'account_id' => 'The selected account is inactive.',
                ]);
            }

            /*
             * 3. Prevent access to another branch's account.
             * A NULL branch_id means a shared account.
             */
            if (
                $account->branch_id !== null &&
                (int) $account->branch_id !== (int) $user->branch_id
            ) {
                throw ValidationException::withMessages([
                    'account_id' =>
                        'You cannot record a collection for this account.',
                ]);
            }

            /*
             * 4. Generate a unique reference automatically.
             * The reference is not entered by the user.
             */
            do {
                $reference = now()->format('YmdHis');

                $referenceExists = DB::table('account_histories')
                    ->where('reference', $reference)
                    ->exists();

            } while ($referenceExists);

            $amount = round((float) $validated['amount'], 2);

            /*
             * 5. Record the cash collection.
             */
            DB::table('account_histories')->insert([
                'branch_id'    => $user->branch_id,
                'user_id'      => $user->id,
                'account_id'   => $account->id,
                'amount'       => number_format($amount, 2, '.', ''),
                'reference'    => $reference,
                'note'         => $validated['note'] ?? null,
                'created_date' => $validated['created_date'],
            ]);

            /*
             * 6. Update the account balance.
             * Use total_balance when available; otherwise use
             * initial_balance, then zero.
             */
            DB::update(
                "UPDATE accounts
                 SET total_balance =
                     CAST(
                         COALESCE(
                             NULLIF(total_balance, ''),
                             initial_balance,
                             0
                         ) AS DECIMAL(20,2)
                     ) + ?,
                     updated_at = NOW()
                 WHERE id = ?",
                [
                    number_format($amount, 2, '.', ''),
                    $account->id,
                ]
            );

        }, 3);

        return redirect()
            ->back()
            ->with(
                'message',
                'Cash collection saved successfully.'
            );

    } catch (ValidationException $e) {
        throw $e;

    } catch (\Throwable $e) {
        Log::error('Cash collection failed', [
            'user_id' => $user->id,
            'account_id' => $validated['account_id'] ?? null,
            'error' => $e->getMessage(),
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                'Unable to save cash collection. Please try again.'
            );
    }
}

    public function ledgerReport(Request $request)
    {
        $accounts = Account::where('branch_id', Auth::user()->branch_id)->orderBy('name')->get();
$categories = DB::table('petty_cash_categories')
    ->orderBy('name')
    ->get();

        $data = $this->getLedgerData($request);

        return view('expenses.ledger', array_merge($data, compact('accounts', 'categories')));
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->getLedgerData($request);

        // Fetch general settings for header
        $settingsRow = DB::table('general_settings')->select('business_name')->first();
        $settings = $settingsRow ? (array) $settingsRow : [];

        $pdf = Pdf::loadView('expenses.ledger-report-pdf', array_merge($data, compact('settings', 'request')));
            // ->setPaper('A4', 'landscape'); // Landscape for wide ledger tables

        $filename = 'ledger-report-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }

    
    private function getLedgerData(Request $request)
{
    $accountId  = $request->account_id;
    $categoryId = $request->category_id;
    $branchId   = Auth::user()->branch_id;

    $startDate = $request->startdate ?: now()->startOfMonth()->format('Y-m-d');
    $endDate   = $request->enddate   ?: now()->format('Y-m-d');

    /*
    |--------------------------------------------------------------------------
    | 1. OPENING BALANCE
    |--------------------------------------------------------------------------
    | Opening = initial_balance + account movements in the PREVIOUS month.
    | The opening row is only built for the FULL ledger (no category filter).
    */
    $openingBalance = 0;
    $openingRow     = null;

    if ($accountId) {
        $account = DB::table('accounts')
            ->where('id', $accountId)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereNull('branch_id');
            })
            ->first();

        if (!$account) {
            abort(403, 'You cannot access this account.');
        }

        $initialBalance = (float) ($account->initial_balance ?? 0);

        $periodStart    = \Illuminate\Support\Carbon::parse($startDate);
        $prevMonthStart = $periodStart->copy()
            ->subMonthNoOverflow()
            ->startOfMonth()
            ->format('Y-m-d');
        $prevMonthEnd   = $periodStart->copy()
            ->subMonthNoOverflow()
            ->endOfMonth()
            ->format('Y-m-d');

        $previousTransactions = DB::table('account_histories')
            ->where('account_id', $accountId)
            ->whereBetween('created_date', [$prevMonthStart, $prevMonthEnd])
            ->where('created_date', '<', $startDate)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereNull('branch_id');
            })
            ->sum(DB::raw("CAST(COALESCE(NULLIF(amount, ''), '0') AS DECIMAL(20,2))"));

        $openingBalance = round($initialBalance + (float) $previousTransactions, 2);

        // Build the opening row ONLY for the full ledger
        if (!$categoryId) {
            $openingRow = [
                'date'        => $startDate,
                'type'        => 'To',
                'particulars' => 'Opening Balance',
                'vch_type'    => '',
                'debit'       => $openingBalance > 0 ? $openingBalance : 0,
                'credit'      => $openingBalance < 0 ? abs($openingBalance) : 0,
                'details'     => [],
                'is_opening'  => true,
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 2. CASH COLLECTIONS  (full ledger only)
    |--------------------------------------------------------------------------
    */
    $histories = collect();

    if (!$categoryId) {
        $histories = DB::table('account_histories')
            ->where('branch_id', $branchId)
            ->whereRaw("CAST(COALESCE(NULLIF(amount, ''), '0') AS DECIMAL(20,2)) > 0")
            ->when($accountId, function ($q) use ($accountId) {
                $q->where('account_id', $accountId);
            })
            ->where('created_date', '>=', $startDate)
            ->where('created_date', '<=', $endDate)
            ->orderBy('created_date')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | 3. EXPENSES  (filtered by category when selected)
    |--------------------------------------------------------------------------
    */
    $expenses = Expense::with('category')
        ->where('branch_id', $branchId)
        ->when($accountId, function ($q) use ($accountId) {
            $q->where('account_id', $accountId);
        })
        ->when($categoryId, function ($q) use ($categoryId) {
            $q->where('expense_category_id', $categoryId);
        })
        ->where('created_date', '>=', $startDate)
        ->where('created_date', '<=', $endDate)
        ->orderBy('created_date')
        ->orderBy('id')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 4. PETTY CASH DETAIL  (only when a category is selected)
    |--------------------------------------------------------------------------
    */
    $petty         = null;
    $petty_cash    = collect();
    $total_amounts = [(object) ['total_amount' => 0]];

    if ($categoryId) {
        $petty      = PettyCashCategory::find($categoryId);
        $petty_cash = $expenses; // already filtered by category

        $total_amounts = [(object) [
            'total_amount' => $petty_cash->sum(fn ($p) => (float) $p->amount),
        ]];
    }

    /*
    |--------------------------------------------------------------------------
    | 5. BUILD PERIOD TRANSACTIONS
    |--------------------------------------------------------------------------
    */
    $transactions = collect();

    // Cash collections (only exist when no category is selected)
    foreach ($histories as $history) {
        $transactions->push([
            'date'        => $history->created_date,
            'type'        => 'To',
            'particulars' => 'Cash Collection',
            'vch_type'    => 'Petty Cash Add.',
            'debit'       => (float) $history->amount,
            'credit'      => 0,
            'details'     => [],
            'is_opening'  => false,
        ]);
    }

    // Expenses grouped by date + voucher
    $groupedExpenses = $expenses->groupBy(function ($item) {
        return $item->created_date . '_' .
            ($item->voucher_no ?: 'reference_' . $item->reference);
    });

    foreach ($groupedExpenses as $group) {
        $first = $group->first();
        $total = $group->sum(fn ($e) => (float) $e->amount);

        $details = [];
        foreach ($group as $expense) {
            $details[] = [
                'category' => $expense->category
                    ? $expense->category->name
                    : 'Misc Expenses',
                'note'   => $expense->note,
                'amount' => (float) $expense->amount,
            ];
        }

        $particulars = $group->count() > 1
            ? '(as per details)'
            : ($first->category
                ? $first->category->name
                : 'Misc Expenses');

        $transactions->push([
            'date'        => $first->created_date,
            'type'        => 'By',
            'particulars' => $particulars,
            'vch_type'    => 'Cash Payment',
            'debit'       => 0,
            'credit'      => (float) $total,
            'details'     => $group->count() > 1 ? $details : [],
            'is_opening'  => false,
        ]);
    }

    $transactions = $transactions->sortBy('date')->values();

    $totalDebit  = round($transactions->sum('debit'),  2);
    $totalCredit = round($transactions->sum('credit'), 2);

    // Opening row is only prepended in the full-ledger view
    $ledger = $openingRow
        ? collect([$openingRow])->concat($transactions)->values()
        : $transactions;

    /*
    |--------------------------------------------------------------------------
    | 6. CLOSING BALANCE
    |--------------------------------------------------------------------------
    */
    $closingBalance = round(
        $openingBalance + $totalDebit - $totalCredit,
        2
    );

    return compact(
        'ledger',
        'totalDebit',
        'totalCredit',
        'openingBalance',
        'closingBalance',
        'accountId',
        'categoryId',
        'startDate',
        'endDate',
        'petty',
        'petty_cash',
        'total_amounts'
    );
}

public function categoryReportPettyCash(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ]);

        $category = PettyCashCategory::findOrFail($validated['category_id']);

        return response()->json($this->buildCategoryFormat($category));
    }

    protected function buildCategoryFormat(Category $category): array
    {
        // If you store report_format as JSON on the category, use it.
        $config = $category->report_format ?? null;

        if (is_string($config)) {
            $config = json_decode($config, true);
        }

        $config = is_array($config) ? $config : [];

        return [
            'id'             => $category->id,
            'name'           => $category->name,
            'format_label'   => $config['format_label'] ?? 'Standard Ledger',
            'description'    => $config['description'] ?? 'Default ledger report for this category.',
            'columns'        => $config['columns'] ?? [
                'Date', 'Reference', 'Description', 'Debit', 'Credit', 'Balance',
            ],
            'extra_fields'   => $config['extra_fields'] ?? [],
        ];
    }
}

