<?php

namespace App\Http\Controllers\Payment;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\MakePaymentRequest;
use App\Models\ChartOfAccount;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\Payment;
use App\Models\TransactionRecorder;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class MakePaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $payments = Payment::where('account_code','like','2%')->paginate(Config::get('common.pagination'));
        $accounts = ChartOfAccount::where('account_type', 1)->where('code', 'like', '2%')->lists('name', 'code');
        $status = Config::get('common.status');
        return view('makePayment.index', compact('payments', 'status', 'accounts'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('account_type', 1)->where('code','like', '2%')->orWhere('code', '32000')->lists('name', 'code');
        $types = Config::get('common.transaction_customer_type');
        $years = CommonHelper::get_years();
        return view('makePayment.create', compact('accounts', 'types', 'years'));
    }

    public function store(MakePaymentRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $payment = New Payment();
                $currentYear = CommonHelper::get_current_financial_year();

                $payment->to_whom_type = $request->to_whom_type;
                $payment->to_whom = $request->to_whom;
                $payment->total_amount = $request->total_amount;
                $payment->amount = $request->amount;
                $payment->transaction_detail = $request->transaction_detail;

                $payment->date = $request->date;
                $payment->year = $currentYear;
                $payment->workspace_id = Auth::user()->workspace_id;
                $payment->account_code = $request->account_code;
                $payment->voucher_no = $request->voucher_no;
                $payment->created_by = Auth::user()->id;
                $payment->created_at = time();
                $payment->save();

                // IMPACTS ON ACCOUNTING TABLES

                $workspace_id = Auth::user()->workspace_id;
                $cashCode = 11000;
                $accountPayableCode = 41000;
                $transaction_type = Config::get('common.transaction_type.payment');

                // Workspace Ledger Account Payable Debit(-)
                $accountReceivableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountPayableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                $accountReceivableWorkspaceData->balance -= $request->amount;
                $accountReceivableWorkspaceData->update();
                // Workspace Ledger Cash Credit(-)
                $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                $cashWorkspaceData->balance += $request->amount;
                $cashWorkspaceData->update();
                // Personal Account balance(-)
                $person_type = $request->to_whom_type;
                $person_id = $request->to_whom;
                $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                $personData->balance -= $request->amount;
                $personData->update();
                // General Journals Account Payable Debit
                $generalJournal = New GeneralJournal;
                $generalJournal->date = strtotime($request->date);
                $generalJournal->transaction_type = $transaction_type;
                $generalJournal->reference_id = $payment->id;
                $generalJournal->year = $currentYear;
                $generalJournal->account_code = $accountPayableCode;
                $generalJournal->workspace_id = $workspace_id;
                $generalJournal->amount = $request->amount;
                $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                $generalJournal->created_by = Auth::user()->id;
                $generalJournal->created_at = time();
                $generalJournal->save();
                // General Journals Cash Credit
                $generalJournal = New GeneralJournal;
                $generalJournal->date = strtotime($request->date);
                $generalJournal->transaction_type = $transaction_type;
                $generalJournal->reference_id = $payment->id;
                $generalJournal->year = $currentYear;
                $generalJournal->account_code = $cashCode;
                $generalJournal->workspace_id = $workspace_id;
                $generalJournal->amount = $request->amount;
                $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $generalJournal->created_by = Auth::user()->id;
                $generalJournal->created_at = time();
                $generalJournal->save();
            });
        } catch (\Exception $e) {
            //dd($e);
            Session()->flash('error_message', 'Payment Creation Failed!');
            return redirect('make_payments');
        }

        Session()->flash('flash_message', 'Payment Done Successfully!');
        return redirect('make_payments');
    }

    public function edit($id)
    {
        Session()->flash('warning_message', 'No Edit Permission!');
        return redirect('recorders');
//        $accounts = ChartOfAccount::where('account_type', 1)->whereIn('code', Config::get('common.transaction_accounts'))->lists('name', 'code');
//        $types = Config::get('common.sales_customer_type');
//        $payment = TransactionRecorder::findOrFail($id);
//        $employees = Employee::where('status', 1)->lists('name', 'id');
//        $suppliers = Supplier::where('status', 1)->lists('company_name', 'id');
//        $customers = Customer::where('status', 1)->lists('name', 'id');
//        $years = CommonHelper::get_years();
//        return view('transactionRecorders.edit', compact('recorder','accounts', 'types', 'employees', 'suppliers', 'customers', 'years'));
    }

    public function update($id, MakePaymentRequest $request)
    {
//        $payment = TransactionRecorder::findOrFail($id);
//        $currentYear = CommonHelper::get_current_financial_year();
//        $slice = substr($request->account_code, 0, 1);
//
//        if ($slice == 1 || $slice == 2 || $slice == 3) {
//            $payment->to_whom_type = $request->to_whom_type;
//            $payment->to_whom = $request->to_whom;
//            $payment->total_amount = $request->total_amount;
//            $payment->amount = $request->amount;
//            $payment->transaction_detail = $request->transaction_detail;
//        } elseif ($slice == 4) {
//            $payment->to_whom_type = $request->to_whom_type;
//            $payment->to_whom = $request->to_whom;
//            $payment->total_amount = $request->total_amount;
//            $payment->amount = $request->amount;
//            $payment->transaction_detail = $request->transaction_detail;
//        } elseif ($slice == 5 || $slice == 6) {
//            $payment->amount = $request->amount;
//        }
//
//        $payment->date = $request->date;
//        $payment->year = $currentYear;
//        $payment->workspace_id = Auth::user()->workspace_id;
//        $payment->account_code = $request->account_code;
//        $payment->updated_by = Auth::user()->id;
//        $payment->updated_at = time();
//        $payment->update();
//
//        Session()->flash('flash_message', 'Transaction Recorder has been updated!');
//        return redirect('recorders');
    }
}
