<?php

namespace App\Http\Controllers\Payroll;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\SalaryPaymentRequest;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\Salary;
use App\Models\SalaryPayment;
use App\Models\WorkspaceLedger;
use Config;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SalaryPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $salaryPayments = SalaryPayment::orderBy('year', 'desc')->orderBy('month', 'desc')->with('employee')->paginate(15);
        return view('payrolls.salaryPayment.index')->with(compact('salaryPayments'));
    }

    public function create()
    {
        return view('payrolls.salaryPayment.create');
    }

    public function store(SalaryPaymentRequest $request)
    {

//        dd($inputs);

        try {
            DB::transaction(function () use ($request) {
                $inputs = $request->input();

                $user = Auth::user();
                $time = time();

                foreach ($inputs['selected'] as $employee_id) {

                    $salary = Salary::find($inputs['employee'][$employee_id]['salary_id']);
                    $copy = clone $salary;
                    $due = $salary->due;
                    $due -= $inputs['employee'][$employee_id]['pay_now'];
                    $salary->paid += $inputs['employee'][$employee_id]['pay_now'];
                    $salary->due -= $inputs['employee'][$employee_id]['pay_now'];
                    if ($due <= 0) {
                        $salary->payment_status = Config::get('common.salary_payment_status.complete'); // Complete payment
                    } else {
                        $salary->payment_status = Config::get('common.salary_payment_status.partial');
                    }
                    $salary->update();

                    if ($copy->payment_status == Config::get('common.salary_payment_status.partial')) {
                        //Update Salary Payment
                        $salaryPayment = SalaryPayment::where(['workspace_id' => $user->workspace_id, 'employee_id' => $employee_id, 'salary_id' => $inputs['employee'][$employee_id]['salary_id']])->first();
                        $salaryPayment->amount += $inputs['employee'][$employee_id]['pay_now'];
                        $salaryPayment->updated_by = $user->id;
                        $salaryPayment->updated_at = $time;
                        $salaryPayment->update();
                    } else {
                        // Insert Salary Payment
                        $salaryPayment = new SalaryPayment();
                        $salaryPayment->salary_id = $inputs['employee'][$employee_id]['salary_id'];
                        $salaryPayment->employee_id = $employee_id;
                        $salaryPayment->workspace_id = $user->workspace_id;
                        $salaryPayment->year = CommonHelper::get_current_financial_year();
                        $salaryPayment->month = $inputs['month'];
                        $salaryPayment->payment_date = $time;
                        $salaryPayment->amount = $inputs['employee'][$employee_id]['pay_now'];
                        $salaryPayment->created_by = $user->id;
                        $salaryPayment->created_at = $time;
                        $salaryPayment->save();
                    }


                    $personalAccount = PersonalAccount::where(['person_id' => $employee_id, 'person_type' => Config::get('common.person_type_employee')])->first();
                    $personalAccount->balance -= $inputs['employee'][$employee_id]['pay_now']; //Subtract
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->save();

                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                    $accountPayableWorkspaceData->balance -= $inputs['employee'][$employee_id]['pay_now']; //Subtract Wage Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                    $accountPayableWorkspaceData->balance -= $inputs['employee'][$employee_id]['pay_now']; //Subtract Cash
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = $time;
                    $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                    $generalJournal->reference_id = $salaryPayment->id;
                    $generalJournal->year = CommonHelper::get_current_financial_year();
                    $generalJournal->account_code = 42000;
                    $generalJournal->workspace_id = $user->workspace_id;
                    $generalJournal->amount = $inputs['employee'][$employee_id]['pay_now'];
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = $user->id;
                    $generalJournal->created_at = $time;
                    $generalJournal->save();

                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = $time;
                    $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                    $generalJournal->reference_id = $salaryPayment->id;
                    $generalJournal->year = CommonHelper::get_current_financial_year();
                    $generalJournal->account_code = 11000;
                    $generalJournal->workspace_id = $user->workspace_id;
                    $generalJournal->amount = $inputs['employee'][$employee_id]['pay_now'];
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = $user->id;
                    $generalJournal->created_at = $time;
                    $generalJournal->save();

                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Salary payment cannot successful. Please Try again.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Salary paid successfully.');
        return Redirect::back();
//        return redirect('salary_payment');
    }


    public function edit($id)
    {
        $salaryPayment = SalaryPayment::find($id);
        return view('payrolls.salaryPayment.edit')->with(compact('salaryPayment'));
    }

    public function update($id, SalaryPaymentRequest $request)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $inputs = $request->input();
                $user = Auth::user();
                $time = time();
                $balance_type = Config::get('common.balance_type_intermediate');
                $year = CommonHelper::get_current_financial_year();
                $salaryPayment = SalaryPayment::find($id);
                $salary = Salary::where(['id' => $salaryPayment->salary_id, 'employee_id' => $salaryPayment->employee_id, 'workspace_id' => $salaryPayment->workspace_id])->first();
                if ($salary->net > $inputs['salary'] && $salaryPayment->amount > $inputs['salary']) {
                    //Decrease amount from Salary payment table
                    $salaryPayment->amount -= $inputs['salary'];
                    $salaryPayment->updated_by = $user->id;
                    $salaryPayment->updated_at = $time;
                    $salaryPayment->update();

                    //Update salaries table
                    $salary->due = $salary->net - $inputs['salary'];
                    $salary->paid = $inputs['salary'];
                    $salary->payment_status = Config::get('common.salary_payment_status.partial');
                    $salary->update();

                    //Update Personal Account
                    $personalAccount = PersonalAccount::where(['person_id' => $salaryPayment->employee_id, 'person_type' => Config::get('common.person_type_employee')])->first();
                    $personalAccount->balance += $salaryPayment->amount - $inputs['salary']; //ADD
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->save();

                    //Update Workspace Ledger
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += $salaryPayment->amount - $inputs['salary']; //Add Wage Payable
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $accountPayableWorkspaceData->balance += $salaryPayment->amount - $inputs['salary']; //Add Cash
                    $accountPayableWorkspaceData->updated_by = $user->id;
                    $accountPayableWorkspaceData->updated_at = $time;
                    $accountPayableWorkspaceData->update();

                    // General Journal Table Impact
                    $generalJournal = GeneralJournal::where(['transaction_type' => Config::get('common.transaction_type.salary_payment'), 'reference_id' => $id, 'account_code' => 42000, 'workspace_id' => $user->workspace_id, 'year' => $year])->first();
                    $generalJournal->amount -= $salaryPayment->amount - $inputs['salary'];
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();

                    $generalJournal = GeneralJournal::where(['transaction_type' => Config::get('common.transaction_type.salary_payment'), 'reference_id' => $id, 'account_code' => 11000, 'workspace_id' => $user->workspace_id, 'year' => $year])->first();
                    $generalJournal->amount -= $salaryPayment->amount - $inputs['salary'];
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->save();
                }

            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Salary payment update cannot successful. Please Try again.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Salary payment update successfully.');
        return redirect('salary_payment');

    }
}
