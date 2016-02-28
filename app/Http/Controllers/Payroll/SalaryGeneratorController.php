<?php

namespace App\Http\Controllers\Payroll;

use App\Helpers\CommonHelper;
use App\Http\Requests\SalaryGeneratorRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\Salary;
use App\Models\WorkspaceLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use DB;

class SalaryGeneratorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $lists = Salary::where(['workspace_id' => Auth::user()->workspace_id, 'status' => 1])->with('employee')->paginate(15);
        return view('payrolls.salaryGenerator.index')->with(compact('lists'));
    }

    public function create()
    {
        return view('payrolls.salaryGenerator.create');
    }

    public function store(SalaryGeneratorRequest $request)
    {
//        dd($request->input());
        try {

            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $user = Auth::user();
                $time = time();
                $total = 0;
                foreach ($inputs['selected'] as $employee_id) {
                    $salary = new Salary();
                    $salary->employee_id = $employee_id;
                    $salary->employee_type = $inputs['employee'][$employee_id]['employee_type'];
                    $salary->workspace_id = $inputs['employee'][$employee_id]['workspace_id'];
                    $salary->year = CommonHelper::get_current_financial_year();
                    $salary->month = $inputs['month'];
                    $salary->salary = $inputs['employee'][$employee_id]['salary'];
                    $salary->extra_hours = $inputs['employee'][$employee_id]['overtime'];
                    $salary->bonus = $inputs['employee'][$employee_id]['bonus'];
                    $salary->cut = $inputs['employee'][$employee_id]['cut'];
                    $salary->due = $inputs['employee'][$employee_id]['net'];
                    $salary->net = $inputs['employee'][$employee_id]['net'];
                    $salary->created_by = $user->id;
                    $salary->created_by = $time;
                    $salary->save();

                    $personalAccount = PersonalAccount::where(['person_id' => $employee_id, 'person_type' => Config::get('common.person_type_employee')])->first();
                    $personalAccount->balance += $inputs['employee'][$employee_id]['net']; //Add
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->save();

                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                    $accountPayableWorkspaceData->balance += $inputs['employee'][$employee_id]['net']; //Add Salary Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 22000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                    $accountPayableWorkspaceData->balance += $inputs['employee'][$employee_id]['net']; //Add Salary Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = $time;
                    $generalJournal->transaction_type = Config::get('common.transaction_type.salary');
                    $generalJournal->reference_id = $salary->id;
                    $generalJournal->year = CommonHelper::get_current_financial_year();
                    $generalJournal->account_code = 42000;
                    $generalJournal->workspace_id = $user->workspace_id;
                    $generalJournal->amount = $inputs['employee'][$employee_id]['net'];
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = $user->id;
                    $generalJournal->created_at = $time;
                    $generalJournal->save();

                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = $time;
                    $generalJournal->transaction_type = Config::get('common.transaction_type.salary');
                    $generalJournal->reference_id = $salary->id;
                    $generalJournal->year = CommonHelper::get_current_financial_year();
                    $generalJournal->account_code = 22000;
                    $generalJournal->workspace_id = $user->workspace_id;
                    $generalJournal->amount = $inputs['employee'][$employee_id]['net'];
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = $user->id;
                    $generalJournal->created_at = $time;
                    $generalJournal->save();

                }


            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Salary cannot generate. Please Try again.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Salary generated successfully.');
        return redirect('salary_generator');


    }


    public function edit($id)
    {
        $salary = Salary::where(['id' => $id, 'status' => 1])->with(['employee', 'employee.designation'])->first();
//dd($salary);
        return view('payrolls.salaryGenerator.edit')->with(compact('salary'));
    }

    public function update($id, SalaryGeneratorRequest $request)
    {
        try {

            DB::transaction(function () use ($request,$id) {
                $user = Auth::user();
                $time = time();
                $inputs = $request->input();
                $salary = Salary::find($id);
                $copy = clone $salary;
                $salary->extra_hours = $inputs['extra_hours'];
                $salary->cut = $inputs['cut'];
                $salary->bonus = $inputs['bonus'];
                $salary->net = $inputs['net'];
                $salary->due = $inputs['net'];
                $salary->save();

                if ($inputs['net'] > $copy->net) {
                    $balance = $inputs['net'] - $copy->net;

                    $personalAccount = PersonalAccount::where(['person_id' => $copy->employee_id, 'person_type' => Config::get('common.person_type_employee')])->first();
                    $personalAccount->balance += $balance; //Add
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->save();

                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                    $accountPayableWorkspaceData->balance += $balance; //Add Salary Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 22000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                    $accountPayableWorkspaceData->balance += $balance; //Add Salary Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = GeneralJournal::where(['transaction_type' => Config::get('common.transaction_type.salary'), 'reference_id' => $id, 'account_code' => 42000])->first();
                    $generalJournal->amount += $balance;
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();

                    $generalJournal = GeneralJournal::where(['transaction_type' => Config::get('common.transaction_type.salary'), 'reference_id' => $id, 'account_code' => 22000])->first();
                    $generalJournal->amount += $balance;
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();
                } elseif ($inputs['net'] < $copy->net) {
                    $balance = $copy->net - $inputs['net'];

                    $personalAccount = PersonalAccount::where(['person_id' => $copy->employee_id, 'person_type' => Config::get('common.person_type_employee')])->first();
                    $personalAccount->balance -= $balance; //Add
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->save();

                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                    $accountPayableWorkspaceData->balance -= $balance; //Add Salary Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 22000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                    $accountPayableWorkspaceData->balance -= $balance; //Add Salary Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = GeneralJournal::where(['transaction_type' => Config::get('common.transaction_type.salary'), 'reference_id' => $id, 'account_code' => 42000])->first();
                    $generalJournal->amount -= $balance;
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();

                    $generalJournal = GeneralJournal::where(['transaction_type' => Config::get('common.transaction_type.salary'), 'reference_id' => $id, 'account_code' => 22000])->first();
                    $generalJournal->amount -= $balance;
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();
                }
            });
        } catch (\Exception $e) {
            dd($e);
            Session()->flash('error_message', 'Salary cannot Update. Please Try again.');
            return Redirect::back();
        }
        Session()->flash('flash_message', 'Salary updated successfully.');
        return redirect('salary_generator');
    }
}
