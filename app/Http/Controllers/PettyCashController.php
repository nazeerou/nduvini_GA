<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use DB;
use App\Models\AccountStatement;
use App\Models\PettyCashCategory;
use App\Models\AccountHistory;
use App\Models\Account;

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
}

