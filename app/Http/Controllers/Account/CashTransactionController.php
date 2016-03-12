<?php

namespace App\Http\Controllers\Account;

use App\Http\Requests;
use App\Models\CashTransaction;
use App\Models\ChartOfAccount;
use App\Models\Workspace;
use App\Models\WorkspaceLedger;
use App\Models\GeneralJournal;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CashTransactionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class CashTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $workspace_id = Auth::user()->workspace_id;
        $workspaces = Workspace::where('status', 1)->lists('name', 'id');
        $transactions = CashTransaction::where('workspace_from', $workspace_id)->orWhere('workspace_to', $workspace_id)->paginate(Config::get('common.pagination'));
        return view('cashTransaction.index', compact('transactions', 'workspaces'));
    }

    public function create()
    {
        $workspace_id = Auth::user()->workspace_id;
        $workspaces = Workspace::where('id', '<>', $workspace_id)->lists('name', 'id'); // <> is equivalent to !=
        $totalCash = WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'account_code'=>11000, 'year'=>CommonHelper::get_current_financial_year(), 'balance_type'=>Config::get('common.balance_type_intermediate')])->value('balance');
        return view('cashTransaction.create', compact('totalCash', 'workspaces'));
    }

    public function store(CashTransactionRequest $request)
    {
        try
        {
            DB::transaction(function () use ($request)
            {
                $workspace_id = Auth::user()->workspace_id;
                $cashTransaction = New CashTransaction;
                $cashTransaction->workspace_from = $workspace_id;
                $cashTransaction->amount = $request->amount;
                $cashTransaction->workspace_to = $request->workspace_to;
                $cashTransaction->sending_date = $request->date;
                $cashTransaction->created_by = Auth::user()->id;
                $cashTransaction->created_at = time();
                $cashTransaction->save();
            });
        }
        catch (\Exception $e)
        {
            Session()->flash('error_message', 'Cash has been sent!');
            return redirect('cash_transaction');
        }

        Session()->flash('flash_message', 'Cash has been sent!');
        return redirect('cash_transaction');
    }

    public function edit($id)
    {
        try
        {
            DB::transaction(function () use ($id)
            {
                $cashCode = 11000;
                $currentYear = CommonHelper::get_current_financial_year();
                $workspace_id = Auth::user()->workspace_id;
                $cashTransaction = CashTransaction::findOrFail($id);
                $cashTransaction->received = 1;
                $cashTransaction->receiving_date = date('d-m-Y');
                $cashTransaction->updated_by = Auth::user()->id;
                $cashTransaction->updated_at = time();
                $cashTransaction->update();

                // Workspace Ledger Impact
                // Workspace Ledger Cash Credit(-)
                $cashWorkspaceDataSender = WorkspaceLedger::where(['workspace_id'=>$cashTransaction->workspace_from, 'account_code'=>$cashCode,'balance_type'=>Config::get('common.balance_type_intermediate'),'year'=>$currentYear])->first();
                $cashWorkspaceDataSender->balance -= $cashTransaction->amount;
                $cashWorkspaceDataSender->update();
                // Workspace Ledger Cash Debit(+)
                $cashWorkspaceDataReceiver = WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'account_code'=>$cashCode,'balance_type'=>Config::get('common.balance_type_intermediate'),'year'=>$currentYear])->first();
                $cashWorkspaceDataReceiver->balance += $cashTransaction->amount;
                $cashWorkspaceDataReceiver->update();

                // General Journal Impact
                // Sender Cash Account Credit
                $generalJournal = New GeneralJournal;
                $generalJournal->date = time();
                $generalJournal->transaction_type = Config::get('common.transaction_type.cash_transfer');
                $generalJournal->year = $currentYear;
                $generalJournal->account_code = $cashCode;
                $generalJournal->workspace_id = $cashTransaction->workspace_from;
                $generalJournal->amount = $cashTransaction->amount;
                $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $generalJournal->created_by = Auth::user()->id;
                $generalJournal->created_at = time();
                $generalJournal->save();
                // Receiver Cash Account Debit
                $generalJournal = New GeneralJournal;
                $generalJournal->date = time();
                $generalJournal->transaction_type = Config::get('common.transaction_type.cash_transfer');
                $generalJournal->year = $currentYear;
                $generalJournal->account_code = $cashCode;
                $generalJournal->workspace_id = $workspace_id;
                $generalJournal->amount = $cashTransaction->amount;
                $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                $generalJournal->created_by = Auth::user()->id;
                $generalJournal->created_at = time();
                $generalJournal->save();
            });
        }
        catch (\Exception $e)
        {
            dd($e);
            Session()->flash('error_message', 'Cash Not Received!');
            return redirect('cash_transaction');
        }

        Session()->flash('flash_message', 'Cash Received & Account Debited Successfully!');
        return redirect('cash_transaction');
    }
}
