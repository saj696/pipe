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
        $this->middleware('perm');
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
        $types = Config::get('common.transaction_customer_type');
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
                    $accountReceivableWorkspaceData->balance -= $request->amount;
                    $accountReceivableWorkspaceData->update();
                    // Workspace Ledger Cash Debit(+)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance += $request->amount;
                    $cashWorkspaceData->update();
                    // Personal Account Due(-)
                    $person_type = $request->from_whom_type;
                    $person_id = $request->from_whom;
                    $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                    $personData->due -= $request->amount;
                    $personData->update();
                    // General Journals Account Receivable Credit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $accountReceivableCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Cash Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 41000) {
                    // ACCOUNT PAYABLE
                    // Account Payable Debit(-)
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountPayableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountPayableWorkspaceData->balance -= $request->amount;
                    $accountPayableWorkspaceData->update();
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance -= $request->amount;
                    $cashWorkspaceData->update();
                    // Personal Account Balance(-)
                    $person_type = $request->to_whom_type;
                    $person_id = $request->to_whom;
                    $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                    $personData->balance -= $request->amount;
                    $personData->update();
                    // General Journals Account Payable Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
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
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 30000) {
                    // REVENUE
                    $due = $request->total_amount - $request->amount;
                    // Revenue Credit(+) with Total Amount
                    $revenueWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $revenueCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $revenueWorkspaceData->balance += $request->total_amount;
                    $revenueWorkspaceData->update();
                    // Workspace Ledger Cash Debit(+)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance += $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Account Receivable Debit(+)
                    $accountReceivableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountReceivableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountReceivableWorkspaceData->balance += $due;
                    $accountReceivableWorkspaceData->update();

                    if ($due > 0) {
                        // Personal Account Due(+)
                        $person_type = $request->from_whom_type;
                        $person_id = $request->from_whom;
                        $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                        $personData->due += $due;
                        $personData->update();

                        // General Journals Account Receivable Debit with due
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($request->date);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                        $generalJournal->reference_id = $recorder->id;
                        $generalJournal->year = $currentYear;
                        $generalJournal->account_code = $accountReceivableCode;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $due;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    }
                    // General Journals Revenue Credit with total amount
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $revenueCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->total_amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Cash Debit with paid
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 20000) {
                    // EXPENSE
                    // Workspace Ledger Expense Account Debit(+)
                    $expenseWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $expenseCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $expenseWorkspaceData->balance += $request->total_amount;
                    $expenseWorkspaceData->update();
                    // Workspace Ledger Cash Credit(-)
                    $due = $request->total_amount - $request->amount;
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance -= $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Account Payable Credit with Due(+)
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountPayableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountPayableWorkspaceData->balance += $due;
                    $accountPayableWorkspaceData->update();

                    if ($due > 0) {
                        // Personal Account Due(+)
                        $person_type = $request->from_whom_type;
                        $person_id = $request->from_whom;
                        $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                        $personData->balance += $due;
                        $personData->update();
                        // General Journals Account Payable Credit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($request->date);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                        $generalJournal->reference_id = $recorder->id;
                        $generalJournal->year = $currentYear;
                        $generalJournal->account_code = $accountPayableCode;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $due;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    }
                    // General Journals Expense Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $expenseCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->total_amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Cash Credit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 50000) {
                    // DRAW
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance -= $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Draw Debit(+)
                    $drawWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $drawCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $drawWorkspaceData->balance += $request->amount;
                    $drawWorkspaceData->update();
                    // General Journals Draw Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $drawCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Cash Credit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
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
                    // General Journals Investment Credit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $investmentCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Cash Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_code == 27000) {
                    // OFFICE SUPPLIES
                    // Workspace Ledger Office Supplies Account Debit(+)
                    $due = $request->total_amount - $request->amount;
                    $ofcSuppliesWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $officeSuppliesCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $ofcSuppliesWorkspaceData->balance += $request->total_amount;
                    $ofcSuppliesWorkspaceData->update();
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance -= $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Account Payable Credit with Due(+)
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountPayableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountPayableWorkspaceData->balance += $due;
                    $accountPayableWorkspaceData->update();
                    if ($due > 0) {
                        // Personal Account Due(+)
                        $person_type = $request->from_whom_type;
                        $person_id = $request->from_whom;
                        $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                        $personData->balance += $due;
                        $personData->update();
                        // General Journals Account Payable Credit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($request->date);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                        $generalJournal->reference_id = $recorder->id;
                        $generalJournal->year = $currentYear;
                        $generalJournal->account_code = $accountPayableCode;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $due;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    }
                    // General Journals Office Supply Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $officeSuppliesCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->total_amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Cash Credit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                }
                elseif($request->account_code == 29000 || $request->account_code == 29100 || $request->account_code == 29200 || $request->account_code == 29300 || $request->account_code == 29400 || $request->account_code == 29500 || $request->account_code == 29600 || $request->account_code == 29700 || $request->account_code == 29800 || $request->account_code == 29910 || $request->account_code == 29950 || $request->account_code == 29980 || $request->account_code == 29990 || $request->account_code == 29991 || $request->account_code == 23000)
                {
                    $due = $request->total_amount - $request->amount;
                    $accountCode = $request->account_code;
                    // Workspace Ledger Account Debit(+)
                    $repairWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $repairWorkspaceData->balance += $request->total_amount;
                    $repairWorkspaceData->update();
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance -= $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Account Payable Credit with Due(+)
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountPayableCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $accountPayableWorkspaceData->balance += $due;
                    $accountPayableWorkspaceData->update();

                    if ($due > 0) {
                        // Personal Account Due(+)
                        $person_type = $request->from_whom_type;
                        $person_id = $request->from_whom;
                        $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                        $personData->balance += $due;
                        $personData->update();
                        // General Journals Account Payable Credit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($request->date);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                        $generalJournal->reference_id = $recorder->id;
                        $generalJournal->year = $currentYear;
                        $generalJournal->account_code = $accountPayableCode;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $due;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    }

                    // General Journals Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $accountCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->total_amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Cash Credit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                }
                elseif($request->account_code == 29930 || $request->account_code == 29960 || $request->account_code == 29996)
                {
                    // DONATION, JAKAT or Middleman Commission
                    $accountCode = $request->account_code;
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance -= $request->amount;
                    $cashWorkspaceData->update();
                    // Workspace Ledger Account Debit(+)
                    $repairWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $repairWorkspaceData->balance += $request->amount;
                    $repairWorkspaceData->update();
                    // General Journals Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $accountCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Credit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                }
                elseif($request->account_code == 29940)
                {
                    $accountCode = $request->cash_adjustment_type;

                    if($accountCode==29994)
                    {
                        // Invisible Expense
                        // Workspace Ledger Invisible Expense Account Debit(+)
                        $WorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                        $WorkspaceData->balance += $request->amount;
                        $WorkspaceData->update();
                        // Workspace Ledger Cash Credit(-)
                        $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                        $cashWorkspaceData->balance -= $request->amount;
                        $cashWorkspaceData->update();
                        // General Journals Invisible Expense Debit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($request->date);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                        $generalJournal->reference_id = $recorder->id;
                        $generalJournal->year = $currentYear;
                        $generalJournal->account_code = $accountCode;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $request->amount;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                        // General Journals Cash Credit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($request->date);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                        $generalJournal->reference_id = $recorder->id;
                        $generalJournal->year = $currentYear;
                        $generalJournal->account_code = $cashCode;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $request->amount;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    }
                    elseif($request->cash_adjustment_type==37000)
                    {
                        // Invisible Income
                        // Workspace Ledger Invisible Income Account Credit(+)
                        $WorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                        $WorkspaceData->balance += $request->amount;
                        $WorkspaceData->update();
                        // Workspace Ledger Cash Debit(+)
                        $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                        $cashWorkspaceData->balance += $request->amount;
                        $cashWorkspaceData->update();
                        // General Journals Invisible Income Credit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($request->date);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                        $generalJournal->reference_id = $recorder->id;
                        $generalJournal->year = $currentYear;
                        $generalJournal->account_code = $accountCode;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $request->amount;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                        // General Journals Cash Debit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($request->date);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                        $generalJournal->reference_id = $recorder->id;
                        $generalJournal->year = $currentYear;
                        $generalJournal->account_code = $cashCode;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $request->amount;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    }
                }
                elseif($request->account_code == 12100)
                {
                    // Loan Receive
                    $accountCode = 41400;
                    // Workspace Ledger Loan Payable Account Credit(+)
                    $WorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $WorkspaceData->balance += $request->amount;
                    $WorkspaceData->update();
                    // Workspace Ledger Cash Debit(+)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance += $request->amount;
                    $cashWorkspaceData->update();
                    // Personal Account balance(+)
                    $person_type = $request->from_whom_type;
                    $person_id = $request->from_whom;
                    $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                    $personData->balance += $request->amount;
                    $personData->update();
                    // General Journals Loan Payable Credit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $accountCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Cash Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                }
                elseif($request->account_code == 41400)
                {
                    // Loan Pay
                    $accountCode = $request->account_code;
                    // Workspace Ledger Loan Payable Account Debit(-)
                    $WorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $WorkspaceData->balance -= $request->amount;
                    $WorkspaceData->update();
                    // Workspace Ledger Cash Credit(-)
                    $cashWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $cashCode, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $cashWorkspaceData->balance -= $request->amount;
                    $cashWorkspaceData->update();
                    // Personal Account balance(-)
                    $person_type = $request->from_whom_type;
                    $person_id = $request->from_whom;
                    $personData = PersonalAccount::where(['person_id' => $person_id, 'person_type' => $person_type])->first();
                    $personData->balance -= $request->amount;
                    $personData->update();
                    // General Journals Loan Payable Debit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $accountCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                    // General Journals Cash Credit
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = strtotime($request->date);
                    $generalJournal->transaction_type = Config::get('common.transaction_type.general');
                    $generalJournal->reference_id = $recorder->id;
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = $cashCode;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                }
            });
        } catch (\Exception $e) {
            //dd($e);
            Session()->flash('error_message', 'Transaction Recorder Creation Failed.');
            return redirect('recorders');
        }

        Session()->flash('flash_message', 'Transaction Recorder Creation Successful.');
        return redirect('recorders');
    }

    public function edit($id)
    {
        Session()->flash('warning_message', 'No Edit Permission!');
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
//        $recorder = TransactionRecorder::findOrFail($id);
//        $currentYear = CommonHelper::get_current_financial_year();
//        $slice = substr($request->account_code, 0, 1);
//
//        if ($slice == 1 || $slice == 2 || $slice == 3) {
//            $recorder->from_whom_type = $request->from_whom_type;
//            $recorder->from_whom = $request->from_whom;
//            $recorder->total_amount = $request->total_amount;
//            $recorder->amount = $request->amount;
//            $recorder->transaction_detail = $request->transaction_detail;
//        } elseif ($slice == 4) {
//            $recorder->to_whom_type = $request->to_whom_type;
//            $recorder->to_whom = $request->to_whom;
//            $recorder->total_amount = $request->total_amount;
//            $recorder->amount = $request->amount;
//            $recorder->transaction_detail = $request->transaction_detail;
//        } elseif ($slice == 5 || $slice == 6) {
//            $recorder->amount = $request->amount;
//        }
//
//        $recorder->date = $request->date;
//        $recorder->year = $currentYear;
//        $recorder->workspace_id = Auth::user()->workspace_id;
//        $recorder->account_code = $request->account_code;
//        $recorder->updated_by = Auth::user()->id;
//        $recorder->updated_at = time();
//        $recorder->update();
//
//        Session()->flash('flash_message', 'Transaction Recorder has been updated!');
//        return redirect('recorders');
    }
}
