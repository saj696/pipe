<?php

namespace App\Http\Controllers\Account;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\TransactionRecorderRequest;
use App\Models\ChartOfAccount;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\TransactionRecorder;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class TransactionRecordersController extends Controller
{
    public function __construct()
    {
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $recorders = TransactionRecorder::paginate(Config::get('common.pagination'));
        $accounts = ChartOfAccount::where('account_type', 1)->lists('name', 'code');
        $status = Config::get('common.status');
        return view('transactionRecorders.index', compact('recorders', 'status', 'accounts'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('account_type', 1)->lists('name', 'code');
        $types = Config::get('common.sales_customer_type');
        $years = CommonHelper::get_years();
        return view('transactionRecorders.create', compact('accounts', 'types', 'years'));
    }

    public function store(TransactionRecorderRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $recorder = New TransactionRecorder;
                $slice = substr($request->account_code, 0, 1);
                $currentYear = CommonHelper::get_current_financial_year();

                if ($slice == 1 || $slice == 2 || $slice == 3) {
                    $recorder->from_whom_type = $request->from_whom_type;
                    $recorder->from_whom = $request->from_whom;
                    $recorder->total_amount = $request->total_amount;
                    $recorder->amount = $request->amount;
                    $recorder->transaction_detail = $request->transaction_detail;
                } elseif ($slice == 4) {
                    $recorder->to_whom_type = $request->to_whom_type;
                    $recorder->to_whom = $request->to_whom;
                    $recorder->total_amount = $request->total_amount;
                    $recorder->amount = $request->amount;
                    $recorder->transaction_detail = $request->transaction_detail;
                } elseif ($slice == 5 || $slice == 6) {
                    $recorder->amount = $request->amount;
                }

                $recorder->date = $request->date;
                $recorder->year = $currentYear;
                $recorder->workspace_id = Auth::user()->workspace_id;
                $recorder->account_code = $request->account_code;
                $recorder->created_by = Auth::user()->id;
                $recorder->created_at = time();
                $recorder->save();

                // IMPACTS ON ACCOUNTING TABLES

                $workspace_id = Auth::user()->workspace_id;
                $cashCode = 11000;
                $accountReceivableCode = 12000;
                $accountPayableCode = 41000;
                $revenueCode = 30000;
                $expenseCode = 20000;
                $drawCode = 50000;
                $investmentCode = 60000;
                $officeSuppliesCode = 27000;

                if ($request->account_code == 12000) {
                    // ACCOUNT RECEIVABLE
                    // Workspace Ledger Account Receivable Credit(-)
                    $accountReceivableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountReceivableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountReceivableWorkspaceData->balance = $accountReceivableWorkspaceData->balance - $request->amount;
                    $accountReceivableWorkspaceData->update();
                    // Workspace Ledger Cash Debit(+)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance += $request->amount;
                    $cashWorkspaceData->update();
                    // Personal Account Due(-)
                    $person_type = $request->from_whom_type;
                    $person_id = $request->from_whom;
                    $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                    $personData->due = $personData->due - $request->amount;
                    $personData->update();
                    // General Journals Insert
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = time();
                    $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                    $generalJournal->reference_id = $person_id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $accountReceivableCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 41000) {
                    // ACCOUNT PAYABLE
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance = $cashWorkspaceData->balance - $request->amount;
                    $cashWorkspaceData->update();
                    // Account Payable Debit(-)
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountPayableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountPayableWorkspaceData->balance = $accountPayableWorkspaceData->balance - $request->amount;
                    $accountPayableWorkspaceData->update();
                    // Personal Account Balance(-)
                    $person_type = $request->to_whom_type;
                    $person_id = $request->to_whom;
                    $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                    $personData->balance = $personData->balance - $request->amount;
                    $personData->update();
                    // General Journals Insert
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = time();
                    $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                    $generalJournal->reference_id = $person_id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $accountPayableCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 30000) {
                    // REVENUE
                    // Workspace Ledger Cash Debit(+)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance += $request->amount;
                    $cashWorkspaceData->update();
                    // Revenue Credit(-) with Total Amount
                    $revenueWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $revenueCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $revenueWorkspaceData->balance = $revenueWorkspaceData->balance - $request->total_amount;
                    $revenueWorkspaceData->update();
                    // Workspace Ledger Account Receivable Debit(+)
                    $accountReceivableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountReceivableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountReceivableWorkspaceData->balance += ($request->total_amount - $request->amount);
                    $accountReceivableWorkspaceData->update();
                    if (($request->total_amount - $request->amount) > 0) {
                        // Personal Account Due(+)
                        $person_type = $request->from_whom_type;
                        $person_id = $request->from_whom;
                        $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                        $personData->due += ($request->total_amount - $request->amount);
                        $personData->update();
                    }
                    // General Journals Insert
                    $person_id = $request->from_whom;
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = time();
                    $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                    $generalJournal->reference_id = $person_id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $revenueCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 20000) {
                    // EXPENSE
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance = $cashWorkspaceData->balance - $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Account Payable Credit with Due(-)
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountPayableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountPayableWorkspaceData->balance = $accountPayableWorkspaceData->balance - ($request->total_amount - $request->amount);
                    $accountPayableWorkspaceData->update();
                    // Workspace Ledger Expense Account Debit(+)
                    $expenseWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $expenseCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $expenseWorkspaceData->balance += $request->amount;
                    $expenseWorkspaceData->update();
                    // General Journals Insert
                    $person_id = $request->from_whom;
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = time();
                    $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                    $generalJournal->reference_id = $person_id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $expenseCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 50000) {
                    // DRAW
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance = $cashWorkspaceData->balance - $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Draw Account Debit(+)
                    $drawWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $drawCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $drawWorkspaceData->balance += $request->amount;
                    $drawWorkspaceData->update();
                    // General Journals Insert
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = time();
                    $generalJournal->transaction_type = Config::get('common.transaction_type.draw');
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $drawCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 60000) {
                    // OWNERS INVESTMENT
                    // Workspace Ledger Cash Debit(+)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance += $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Investment Account Credit(+)
                    $investmentWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $investmentCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $investmentWorkspaceData->balance += $request->amount;
                    $investmentWorkspaceData->update();
                    // General Journals Insert
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = time();
                    $generalJournal->transaction_type = Config::get('common.transaction_type.investment');
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $investmentCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 27000) {
                    // OFFICE SUPPLIES
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance = $cashWorkspaceData->balance - $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Account Payable Credit with Due(-)
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountPayableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountPayableWorkspaceData->balance = $accountPayableWorkspaceData->balance - ($request->total_amount - $request->amount);
                    $accountPayableWorkspaceData->update();
                    // Workspace Ledger Office Supplies Account Debit(+)
                    $ofcSuppliesWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $officeSuppliesCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $ofcSuppliesWorkspaceData->balance += $request->amount;
                    $ofcSuppliesWorkspaceData->update();
                    // General Journals Insert
                    $person_id = $request->from_whom;
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = time();
                    $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                    $generalJournal->reference_id = $person_id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $officeSuppliesCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Transaction Recorder Creation Failed.');
            return redirect('recorders');
        }

        Session()->flash('flash_message', 'Transaction Recorder Creation Successful.');
        return redirect('recorders');
    }

    public function edit($id)
    {
        Session()->flash('flash_message', 'No Edit Permission!');
        return redirect('recorders');
//        $accounts = ChartOfAccount::where('account_type', 1)->whereIn('code', Config::get('common.transaction_accounts'))->lists('name', 'code');
//        $types = Config::get('common.sales_customer_type');
//        $recorder = TransactionRecorder::findOrFail($id);
//        $employees = Employee::where('status', 1)->lists('name', 'id');
//        $suppliers = Supplier::where('status', 1)->lists('company_name', 'id');
//        $customers = Customer::where('status', 1)->lists('name', 'id');
//        $years = CommonHelper::get_years();
//        return view('transactionRecorders.edit', compact('recorder','accounts', 'types', 'employees', 'suppliers', 'customers', 'years'));
    }

    public function update($id, TransactionRecorderRequest $request)
    {
        $recorder = TransactionRecorder::findOrFail($id);
        $currentYear = CommonHelper::get_current_financial_year();
        $slice = substr($request->account_code, 0, 1);

        if ($slice == 1 || $slice == 2 || $slice == 3) {
            $recorder->from_whom_type = $request->from_whom_type;
            $recorder->from_whom = $request->from_whom;
            $recorder->total_amount = $request->total_amount;
            $recorder->amount = $request->amount;
            $recorder->transaction_detail = $request->transaction_detail;
        } elseif ($slice == 4) {
            $recorder->to_whom_type = $request->to_whom_type;
            $recorder->to_whom = $request->to_whom;
            $recorder->total_amount = $request->total_amount;
            $recorder->amount = $request->amount;
            $recorder->transaction_detail = $request->transaction_detail;
        } elseif ($slice == 5 || $slice == 6) {
            $recorder->amount = $request->amount;
        }

        $recorder->date = $request->date;
        $recorder->year = $currentYear;
        $recorder->workspace_id = Auth::user()->workspace_id;
        $recorder->account_code = $request->account_code;
        $recorder->updated_by = Auth::user()->id;
        $recorder->updated_at = time();
        $recorder->update();

        Session()->flash('flash_message', 'Transaction Recorder has been updated!');
        return redirect('recorders');
    }
}
