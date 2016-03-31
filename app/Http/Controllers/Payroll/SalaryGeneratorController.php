<?php

namespace App\Http\Controllers\Payroll;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\SalaryGeneratorRequest;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\Salary;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class SalaryGeneratorController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
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
                $transaction_type = Config::get('common.transaction_type.salary');
                $balance_type = Config::get('common.balance_type_intermediate');
                $year = CommonHelper::get_current_financial_year();
                $total = 0;
                foreach ($inputs['selected'] as $employee_id) {
                    $salary = new Salary();
                    $salary->employee_id = $employee_id;
                    $salary->employee_type = $inputs['employee'][$employee_id]['employee_type'];
                    $salary->workspace_id = $inputs['employee'][$employee_id]['workspace_id'];
                    $salary->year = $year;
                    $salary->month = $inputs['month'];
                    $salary->salary = $inputs['employee'][$employee_id]['salary'];
                    $salary->cut = $inputs['employee'][$employee_id]['cut'];
                    $salary->net = $inputs['employee'][$employee_id]['net'];
                    $salary->net_due = $inputs['employee'][$employee_id]['net'];
                    if ($inputs['employee'][$employee_id]['overtime'] > 0) {
                        $salary->over_time = $inputs['employee'][$employee_id]['overtime'];
                        $salary->over_time_amount = $inputs['employee'][$employee_id]['overtime_amount'];
                        $salary->over_time_due = $inputs['employee'][$employee_id]['overtime_amount'];
                    }
                    if ($inputs['employee'][$employee_id]['bonus'] > 0) {
                        $salary->bonus = $inputs['employee'][$employee_id]['bonus'];
                        $salary->bonus_due = $inputs['employee'][$employee_id]['bonus'];
                    }
                    $salary->created_by = $user->id;
                    $salary->created_by = $time;
                    $salary->save();

                    $personalAccount = PersonalAccount::where(['person_id' => $employee_id, 'person_type' => Config::get('common.person_type_employee')])->first();
                    $personalAccount->balance += $inputs['employee'][$employee_id]['net']; //Add Balance
                    if ($inputs['employee'][$employee_id]['overtime'] > 0) {
                        $personalAccount->overtime_balance += $inputs['employee'][$employee_id]['overtime']; //Add overtime balance
                    }
                    if ($inputs['employee'][$employee_id]['bonus'] > 0) {
                        $personalAccount->bonus_balance += $inputs['employee'][$employee_id]['bonus']; //Add overtime balance
                    }
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->save();

                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += $inputs['employee'][$employee_id]['net']; //Add Salary Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 22000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += $inputs['employee'][$employee_id]['net']; //Add Salary Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    if ($inputs['employee'][$employee_id]['overtime'] > 0) {
                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 44000, 'balance_type' => $balance_type, 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance += $inputs['employee'][$employee_id]['overtime']; //Add Overtime Payable
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $time;
                        $generalJournal->transaction_type = $transaction_type;
                        $generalJournal->reference_id = $salary->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 44000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['employee'][$employee_id]['overtime'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29993, 'balance_type' => $balance_type, 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance += $inputs['employee'][$employee_id]['overtime']; //Add Overtime Expense
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $time;
                        $generalJournal->transaction_type = $transaction_type;
                        $generalJournal->reference_id = $salary->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 29993;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['employee'][$employee_id]['overtime'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();
                    }

                    if ($inputs['employee'][$employee_id]['bonus'] > 0) {
                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 45000, 'balance_type' => $balance_type, 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance += $inputs['employee'][$employee_id]['bonus']; //Add Bonus Expense
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $time;
                        $generalJournal->transaction_type = $transaction_type;
                        $generalJournal->reference_id = $salary->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 45000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['employee'][$employee_id]['bonus'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29970, 'balance_type' => $balance_type, 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance += $inputs['employee'][$employee_id]['bonus']; //Add Bonus Expense
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $time;
                        $generalJournal->transaction_type = $transaction_type;
                        $generalJournal->reference_id = $salary->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 29970;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['employee'][$employee_id]['bonus'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();
                    }

                    // General Journal Table Impact
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = $time;
                    $generalJournal->transaction_type = $transaction_type;
                    $generalJournal->reference_id = $salary->id;
                    $generalJournal->year = $year;
                    $generalJournal->account_code = 42000;
                    $generalJournal->workspace_id = $user->workspace_id;
                    $generalJournal->amount = $inputs['employee'][$employee_id]['net'];
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = $user->id;
                    $generalJournal->created_at = $time;
                    $generalJournal->save();

                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = $time;
                    $generalJournal->transaction_type = $transaction_type;
                    $generalJournal->reference_id = $salary->id;
                    $generalJournal->year = $year;
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

            DB::transaction(function () use ($request, $id) {
                $user = Auth::user();
                $time = time();
                $year = CommonHelper::get_current_financial_year();
                $transaction_type = Config::get('common.transaction_type.salary');
                $balance_type = Config::get('common.balance_type_intermediate');
                $inputs = $request->input();


                $salary = Salary::find($id);
                $copy = clone $salary;

                $salary->cut = $inputs['cut'];
                $salary->net = $inputs['net'];
                $salary->net_due = $inputs['net'];
                if ($inputs['over_time'] > 0) {
                    $salary->over_time = $inputs['over_time'];
                    $salary->over_time_due = $inputs['overtime_amount'];
                    $salary->over_time_amount = $inputs['overtime_amount'];
                }
                if ($inputs['bonus'] > 0) {
                    $salary->bonus = $inputs['bonus'];
                    $salary->bonus_due = $inputs['bonus'];
                }
                $salary->update();


                $personalAccount = PersonalAccount::where(['person_id' => $copy->employee_id, 'person_type' => Config::get('common.person_type_employee')])->first();
                if ($inputs['net'] > $copy->net) {
                    $balance = $inputs['net'] - $copy->net;
                    $personalAccount->balance += $balance; //Add
                } elseif ($inputs['net'] < $copy->net) {
                    $balance = $copy->net - $inputs['net'];
                    $personalAccount->balance -= $balance; //Sub
                }
                if ($inputs['over_time'] > $copy->over_time) {
                    $personalAccount->overtime_balance += ($inputs['over_time'] - $copy->over_time);
                } elseif ($inputs['over_time'] < $copy->over_time) {
                    $personalAccount->overtime_balance -= ($copy->over_time - $inputs['over_time']);
                }
                if ($inputs['bonus'] > $copy->bonus) {
                    $personalAccount->bonus_balance += ($inputs['bonus'] - $copy->bonus);
                } elseif ($inputs['bonus'] < $copy->bonus) {
                    $personalAccount->bonus_balance -= ($copy->bonus - $inputs['bonus']);
                }
                $personalAccount->updated_by = $user->id;
                $personalAccount->updated_at = $time;
                $personalAccount->save();


                if ($inputs['net'] > $copy->net) {
                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += $balance; //Add Salary Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 22000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += $balance; //Add Salary Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 42000, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount += $balance;
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();

                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 22000, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount += $balance;
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();
                } elseif ($inputs['net'] < $copy->net) {
                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance -= $balance; //Add Salary Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 22000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance -= $balance; //Add Salary Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 42000, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount -= $balance;
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();

                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 22000, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount -= $balance;
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();
                }

                if ($inputs['over_time'] > $copy->over_time) {
                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 44000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += ($inputs['overtime_amount'] - $copy->overtime_amount); //Add Overtime Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29993, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += ($inputs['overtime_amount'] - $copy->overtime_amount); //Add Overtime Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 44000, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount += ($inputs['overtime_amount'] - $copy->overtime_amount);
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();

                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 29993, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount += ($inputs['overtime_amount'] - $copy->overtime_amount);
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();
                } elseif ($inputs['over_time'] < $copy->over_time) {
                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 44000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance -= ($copy->overtime_amount - $inputs['overtime_amount']); //Sub Overtime Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29993, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance -= ($copy->overtime_amount - $inputs['overtime_amount']); //Sub Overtime Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 44000, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount -= ($copy->overtime_amount - $inputs['overtime_amount']);
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();

                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 29993, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount -= ($copy->overtime_amount - $inputs['overtime_amount']);
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();
                }

                if ($inputs['bonus'] > $copy->bonus) {
                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 45000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += ($inputs['bonus'] - $copy->bonus); //Add bonus Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29970, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += ($inputs['bonus'] - $copy->bonus); //Add bonus Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 45000, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount += ($inputs['bonus'] - $copy->bonus);
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();

                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 29970, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount += ($inputs['bonus'] - $copy->bonus);
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();
                } elseif ($inputs['bonus'] < $copy->bonus) {
                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 45000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance -= ($copy->bonus - $inputs['bonus']); //Sub bonus Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29970, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance -= ($copy->bonus - $inputs['bonus']); //Sub bonus Expense
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 45000, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount -= ($copy->bonus - $inputs['bonus']);
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();

                    $generalJournal = GeneralJournal::where(['transaction_type' => $transaction_type, 'reference_id' => $id, 'account_code' => 29970, 'year' => $year, 'workspace_id' => $user->workspace_id])->first();
                    $generalJournal->amount -= ($copy->bonus - $inputs['bonus']);
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
