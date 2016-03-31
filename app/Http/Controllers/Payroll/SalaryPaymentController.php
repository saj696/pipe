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

        try {
            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $user = Auth::user();
                $time = time();
                $date = strtotime(date('d-m-Y'));
                $year=CommonHelper::get_current_financial_year();

                $oldPayment = SalaryPayment::where('salary_id', '=', $inputs['salary_id'])->where('status', '!=', 4)->first();

                if (empty($oldPayment)) {

                    $salaryPayment = new SalaryPayment();
                    $salaryPayment->salary_id = $inputs['salary_id'];
                    $salaryPayment->employee_id = $inputs['employee_id'];
                    $salaryPayment->employee_type = Config::get('common.employee_type.Regular');
                    $salaryPayment->workspace_id = $inputs['workspace_id'];
                    $salaryPayment->year = $year;
                    $salaryPayment->month = $inputs['month'];
                    $salaryPayment->payment_date = $time;
                    if (isset($inputs['net_pay'])) {
                        $salaryPayment->net_paid = $inputs['net_pay'];
                    }
                    if (isset($inputs['bonus_pay'])) {
                        $salaryPayment->bonus_paid = $inputs['bonus_pay'];
                    }
                    if (isset($inputs['over_time_pay'])) {
                        $salaryPayment->over_time_paid = $inputs['over_time_pay'];
                    }
                    $salaryPayment->created_by = $user->id;
                    $salaryPayment->created_at = $time;
                    $salaryPayment->save();

                    $salary = Salary::find($inputs['salary_id']);
                    $clone = clone $salary;
                    $net_due = 0;
                    $bonus_due = 0;
                    $overtime_due = 0;
                    if (isset($inputs['net_pay'])) {
                        $salary->net_paid = $net_due = $inputs['net_pay'];
                        $salary->net_due -= $inputs['net_pay'];
                    }
                    if (isset($inputs['bonus_pay'])) {
                        $salary->bonus_paid = $bonus_due = $inputs['bonus_pay'];
                        $salary->bonus_due -= $inputs['bonus_pay'];
                    }
                    if (isset($inputs['over_time_pay'])) {
                        $salary->over_time_paid = $overtime_due = $inputs['over_time_pay'];
                        $salary->over_time_due -= $inputs['over_time_pay'];
                    }

                    if ($net_due == $clone->net_due && $bonus_due == $clone->bonus_due && $overtime_due == $clone->over_time_due) {
                        $salary->status = 4; //Completed

                        //Update Payment
                        $payment = SalaryPayment::find($salaryPayment->id);
                        $payment->status = 4; //Completed
                        $payment->update();
                    }else{
                        $salary->status = 2; //Partial

                        //Update Payment
                        $payment = SalaryPayment::find($salaryPayment->id);
                        $payment->status = 2; //Partial
                        $payment->update();
                    }


                    $salary->updated_by = $user->id;
                    $salary->updated_at = $time;
                    $salary->update();

                    $personalAccount = PersonalAccount::where(['person_id' => $inputs['employee_id'], 'person_type' => Config::get('common.person_type_employee')])->first();
                    if (isset($inputs['net_pay'])) {
                        $personalAccount->balance -= $inputs['net_pay'];
                    }
                    if (isset($inputs['bonus_pay'])) {
                        $personalAccount->bonus_balance -= $inputs['bonus_pay'];
                    }
                    if (isset($inputs['over_time_pay'])) {
                        $personalAccount->overtime_balance -= $inputs['over_time_pay'];
                    }
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                    if (isset($inputs['net_pay']) && $inputs['net_pay'] > 0) {
                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['net_pay']; //Sub Salary Payable
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $salaryPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 42000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['net_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['net_pay']; //Sub Cash
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $salaryPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 11000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['net_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                    }

                    if (isset($inputs['bonus_pay']) && $inputs['bonus_pay'] > 0) {
                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 45000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['bonus_pay']; //Sub Bonus Payable
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $salaryPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 45000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['bonus_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['bonus_pay']; //Sub Cash
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $salaryPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 11000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['bonus_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                    }

                    if (isset($inputs['over_time_pay']) && $inputs['over_time_pay'] > 0) {
                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 44000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['over_time_pay']; //Sub Overtime Payable
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $salaryPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 44000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['over_time_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['over_time_pay']; //Sub Cash
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $salaryPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 11000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['over_time_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();
                    }
                } else {

                    $oldPayment->payment_date = $time;
                    if (isset($inputs['net_pay'])) {
                        $oldPayment->net_paid += $inputs['net_pay'];
                    }
                    if (isset($inputs['bonus_pay'])) {
                        $oldPayment->bonus_paid += $inputs['bonus_pay'];
                    }
                    if (isset($inputs['over_time_pay'])) {
                        $oldPayment->over_time_paid += $inputs['over_time_pay'];
                    }
                    $oldPayment->updated_by = $user->id;
                    $oldPayment->updated_at = $time;
                    $oldPayment->update();

                    $salary = Salary::find($inputs['salary_id']);
                    $clone = clone $salary;
                    $net_due = 0;
                    $bonus_due = 0;
                    $overtime_due = 0;
                    if (isset($inputs['net_pay'])) {
                        $salary->net_paid += $net_due = $inputs['net_pay'];
                        $salary->net_due -= $inputs['net_pay'];
                    }
                    if (isset($inputs['bonus_pay'])) {
                        $salary->bonus_paid += $bonus_due = $inputs['bonus_pay'];
                        $salary->bonus_due -= $inputs['bonus_pay'];
                    }
                    if (isset($inputs['over_time_pay'])) {
                        $salary->over_time_paid += $overtime_due = $inputs['over_time_pay'];
                        $salary->over_time_due -= $inputs['over_time_pay'];
                    }

                    if ($net_due == $clone->net_due && $bonus_due == $clone->bonus_due && $overtime_due == $clone->over_time_due) {
                        $salary->status = 4; //Completed

                        //Update Payment
                        $payment = SalaryPayment::find($oldPayment->id);
                        $payment->status = 4; //Completed
                        $payment->update();
                    }else{
                        $salary->status = 2; //Partial

                        //Update Payment
                        $payment = SalaryPayment::find($oldPayment->id);
                        $payment->status = 2; //Partial
                        $payment->update();
                    }


                    $salary->updated_by = $user->id;
                    $salary->updated_at = $time;
                    $salary->update();

                    $personalAccount = PersonalAccount::where(['person_id' => $inputs['employee_id'], 'person_type' => Config::get('common.person_type_employee')])->first();
                    if (isset($inputs['net_pay'])) {
                        $personalAccount->balance -= $inputs['net_pay'];
                    }
                    if (isset($inputs['bonus_pay'])) {
                        $personalAccount->bonus_balance -= $inputs['bonus_pay'];
                    }
                    if (isset($inputs['over_time_pay'])) {
                        $personalAccount->overtime_balance -= $inputs['over_time_pay'];
                    }
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                    if (isset($inputs['net_pay']) && $inputs['net_pay'] > 0) {
                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 42000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['net_pay']; //Sub Salary Payable
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $data=[
                            'transaction_type' => Config::get('common.transaction_type.salary_payment'),
                            'reference_id'=>$oldPayment->id,
                            'year'=> $year,
                            'account_code'=> 42000,
                            'workspace_id'=>$user->workspace_id
                        ];

                        $generalJournal = GeneralJournal::firstOrCreate($data);
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $oldPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 42000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount += $inputs['net_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['net_pay']; //Sub Cash
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $data=[
                            'transaction_type' => Config::get('common.transaction_type.salary_payment'),
                            'reference_id'=>$oldPayment->id,
                            'year'=> $year,
                            'account_code'=> 11000,
                            'workspace_id'=>$user->workspace_id
                        ];

                        $generalJournal = GeneralJournal::firstOrCreate($data);
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $oldPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 11000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount += $inputs['net_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                    }

                    if (isset($inputs['bonus_pay']) && $inputs['bonus_pay'] > 0) {
                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 45000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['bonus_pay']; //Sub Bonus Payable
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $data=[
                            'transaction_type' => Config::get('common.transaction_type.salary_payment'),
                            'reference_id'=>$oldPayment->id,
                            'year'=> $year,
                            'account_code'=> 45000,
                            'workspace_id'=>$user->workspace_id
                        ];

                        $generalJournal = GeneralJournal::firstOrCreate($data);
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $oldPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 45000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount += $inputs['bonus_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['bonus_pay']; //Sub Cash
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $data=[
                            'transaction_type' => Config::get('common.transaction_type.salary_payment'),
                            'reference_id'=>$oldPayment->id,
                            'year'=> $year,
                            'account_code'=> 11000,
                            'workspace_id'=>$user->workspace_id
                        ];

                        $generalJournal = GeneralJournal::firstOrCreate($data);
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $oldPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 11000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount += $inputs['bonus_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                    }

                    if (isset($inputs['over_time_pay']) && $inputs['over_time_pay'] > 0) {
                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 44000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['over_time_pay']; //Sub Overtime Payable
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $data=[
                            'transaction_type' => Config::get('common.transaction_type.salary_payment'),
                            'reference_id'=>$oldPayment->id,
                            'year'=> $year,
                            'account_code'=> 44000,
                            'workspace_id'=>$user->workspace_id
                        ];

                        $generalJournal = GeneralJournal::firstOrCreate($data);
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $oldPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 44000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount += $inputs['over_time_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $accountPayableWorkspaceData->balance -= $inputs['over_time_pay']; //Sub Cash
                        $accountPayableWorkspaceData->updated_by = $user->id;
                        $accountPayableWorkspaceData->updated_at = $time;
                        $accountPayableWorkspaceData->update();

                        $data=[
                            'transaction_type' => Config::get('common.transaction_type.salary_payment'),
                            'reference_id'=>$oldPayment->id,
                            'year'=> $year,
                            'account_code'=> 11000,
                            'workspace_id'=>$user->workspace_id
                        ];

                        $generalJournal = GeneralJournal::firstOrCreate($data);
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.salary_payment');
                        $generalJournal->reference_id = $oldPayment->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 11000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount += $inputs['over_time_pay'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();
                    }
                }
            });
        } catch (\Exception $e) {
            dd($e);
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
                $date = strtotime(date('d-m-Y'));
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
