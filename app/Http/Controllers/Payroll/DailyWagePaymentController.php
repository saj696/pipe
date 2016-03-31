<?php

namespace App\Http\Controllers\Payroll;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\WageRequest;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\Wage;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class DailyWagePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $wages = Wage::with('employee')->orderBy('payment_date', 'desc')->paginate(15);
        return view('payrolls.dailyWagePayment.index')->with(compact('wages'));
    }

    public function create()
    {
        $current_date = strtotime(date('d-m-Y'));
        $payments = Wage::where('employee_type', '=', Config::get('common.employee_type.Daily Worker'))->where('payment_date', '=', $current_date)->get(['employee_id'])->toArray();
        $emp = array_values(array_column($payments, 'employee_id'));
        $employees = Employee::where('employee_type', '=', Config::get('common.employee_type.Daily Worker'))->whereNotIn('id', $emp)->with('designation')->get();
        return view('payrolls.dailyWagePayment.create')->with(compact('employees'));
    }

    public function store(WageRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
//                dd($request->input());
                $inputs = $request->input();
                $user = Auth::user();
                $time = time();
                $date = strtotime(date('d-m-Y'));
                $balance_type = Config::get('common.balance_type_intermediate');
                $year = CommonHelper::get_current_financial_year();

                foreach ($inputs['selected'] as $key => $employee_id) {
                    $wage = new Wage();
                    $wage->employee_id = $employee_id;
                    $wage->employee_type = Config::get('common.employee_type.Daily Worker');
                    $wage->workspace_id = $inputs['employee'][$employee_id]['workspace_id'];
                    $wage->payment_date = strtotime($inputs['payment_date']);
                    $wage->wage = $inputs['employee'][$employee_id]['wage'];
                    $wage->paid = $inputs['employee'][$employee_id]['pay_now'];
                    $wage->due = $due = $inputs['employee'][$employee_id]['wage'] - $inputs['employee'][$employee_id]['pay_now'];
                    $wage->created_by = $user->id;
                    $wage->created_at = $time;
                    $wage->save();

                    if($inputs['employee'][$employee_id]['pay_now']){
                        $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => $balance_type, 'year' => $year])->first();
                        $workspaceLedger->balance -= $inputs['employee'][$employee_id]['pay_now']; //Sub Cash
                        $workspaceLedger->updated_by = $user->id;
                        $workspaceLedger->updated_at = $time;
                        $workspaceLedger->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.wage_payment');
                        $generalJournal->reference_id = $wage->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 11000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['employee'][$employee_id]['pay_now'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29992, 'balance_type' => $balance_type, 'year' => $year])->first();
                        $workspaceLedger->balance += $inputs['employee'][$employee_id]['pay_now']; //add Wage Expense
                        $workspaceLedger->updated_by = $user->id;
                        $workspaceLedger->updated_at = $time;
                        $workspaceLedger->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.wage_payment');
                        $generalJournal->reference_id = $wage->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 29992;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['employee'][$employee_id]['pay_now'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();
                    }

                    if ($due > 0) {
                        $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 43000, 'balance_type' => $balance_type, 'year' => $year])->first();
                        $workspaceLedger->balance += $due; //add Wage Payable
                        $workspaceLedger->updated_by = $user->id;
                        $workspaceLedger->updated_at = $time;
                        $workspaceLedger->update();

                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = $date;
                        $generalJournal->transaction_type = Config::get('common.transaction_type.wage_payment');
                        $generalJournal->reference_id = $wage->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 43000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $due;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = $user->id;
                        $generalJournal->created_at = $time;
                        $generalJournal->save();

                        $personal = PersonalAccount::where('person_id', '=', $employee_id)->where('person_type', '=', Config::get('common.person_type_employee'))->first();
                        $personal->balance += $due;
                        $personal->updated_by = $user->id;
                        $personal->updated_at = $time;
                        $personal->update();
                    }
                }
            });
        } catch (\Exception $e) {
            dd($e);
            Session()->flash('error_message', 'Wage payment can not successfully. Please Try again.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Successfully paid.');
        return redirect('daily_wage_payment');
    }

    public function edit($id)
    {
        $wage = Wage::where('id', '=', $id)->with('employee')->first();
        return view('payrolls.dailyWagePayment.edit')->with(compact('wage'));
    }

    public function update($id, Request $request)
    {
//        dd($request->input());
        try {
            DB::transaction(function () use ($id, $request) {
                $user = Auth::user();
                $time = time();
                $date = strtotime(date('d-m-Y'));
                $balance_type = Config::get('common.balance_type_intermediate');
                $year = CommonHelper::get_current_financial_year();

                $wage = Wage::find($id);
                $clone = clone $wage;
                $wage->wage = $request->wage;
                $wage->paid = $request->paid;
                $wage->due = $request->wage - $request->paid;
                $wage->updated_by = $user->id;
                $wage->updated_at = $time;
                $wage->update();

                if ($clone->paid && !$request->paid) {

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance += $clone->paid; //Add Cash
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 11000)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $generalJournal->delete();

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29992, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance -= $clone->paid; //sub Wage Expense
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 29992)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $workspaceLedger->delete();

                } elseif (!$clone->paid && $request->paid) {

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance -= $request->paid; //Sub Cash
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 11000)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $generalJournal->amount += $request->paid;
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->update();

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29992, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance += $request->paid; //sub Wage Expense
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 29992)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $generalJournal->amount += $request->paid;
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();
                } elseif ($clone->paid > $request->paid) {
                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance += ($clone->paid - $request->paid); //Add Cash
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 11000)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $generalJournal->amount -= ($clone->paid - $request->paid);
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->update();

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29992, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance -= ($clone->paid - $request->paid); //sub Wage Expense
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 29992)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $generalJournal->amount -= ($clone->paid - $request->paid);
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                } elseif ($clone->paid < $request->paid) {

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance -= ($request->paid - $clone->paid); //Sub Cash
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 11000)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $generalJournal->amount += ($request->paid - $clone->paid);
                    $generalJournal->updated_by = $user->id;
                    $generalJournal->updated_at = $time;
                    $generalJournal->update();

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 29992, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance += ($request->paid - $clone->paid); //sub Wage Expense
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 29992)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $generalJournal->amount += ($request->paid - $clone->paid);
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                }

                $due = $request->wage - $request->paid;


                if (!$clone->due && $due) {

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 43000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance += $due; //Add wage payable
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = $date;
                    $generalJournal->transaction_type = Config::get('common.transaction_type.wage_payment');
                    $generalJournal->reference_id = $wage->id;
                    $generalJournal->year = $year;
                    $generalJournal->account_code = 43000;
                    $generalJournal->workspace_id = $user->workspace_id;
                    $generalJournal->amount = $due;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = $user->id;
                    $generalJournal->created_at = $time;
                    $generalJournal->save();

                    $personal = PersonalAccount::where('person_id', '=', $clone->employee_id)->where('person_type', '=', Config::get('common.person_type_employee'))->first();
                    $personal->balance += $due;
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->update();

                } elseif ($clone->due && !$due) {

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 43000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance -= $clone->due; //sub wage payable
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 43000)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $workspaceLedger->delete();

                    $personal = PersonalAccount::where('person_id', '=', $clone->employee_id)->where('person_type', '=', Config::get('common.person_type_employee'))->first();
                    $personal->balance -= $clone->due - $due;
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->update();
                } elseif ($clone->due > $due) {
                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 43000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance -= ($clone->due - $due); //sub wage payable
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 43000)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $generalJournal->amount -= ($clone->due - $due);
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $personal = PersonalAccount::where('person_id', '=', $clone->employee_id)->where('person_type', '=', Config::get('common.person_type_employee'))->first();
                    $personal->balance -= ($clone->due - $due);
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->update();

                } elseif ($clone->due < $due) {

                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 43000, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspaceLedger->balance += ($due - $clone->due); //Add wage payable
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $generalJournal = GeneralJournal::where('transaction_type', '=', Config::get('common.transaction_type.wage_payment'))
                        ->where('reference_id', '=', $id)
                        ->where('year', '=', $year)
                        ->where('account_code', '=', 43000)
                        ->where('workspace_id', '=', $user->workspace_id)
                        ->first();
                    $generalJournal->amount += ($due - $clone->due);
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_at = $time;
                    $workspaceLedger->update();

                    $personal = PersonalAccount::where('person_id', '=', $clone->employee_id)->where('person_type', '=', Config::get('common.person_type_employee'))->first();
                    $personal->balance += ($due - $clone->due);
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->update();

                }

            });
        } catch (\Exception $e) {
            dd($e);
            Session()->flash('error_message', 'Wage payment update can not successfully. Please Try again.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Successfully Updated.');
        return redirect('daily_wage_payment');
    }

}
