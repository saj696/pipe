<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\BankTransactionRequest;
use App\Models\Bank;
use App\Models\BankTransaction;
use App\Models\ChartOfAccount;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\WorkspaceLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class BankTransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $transactions = BankTransaction::with('bank')
            ->select('*')
            ->where(['status' => 1])
            ->paginate(Config::get('common.pagination'));

        return view('bankTransactions.index')->with('transactions', $transactions);
    }

    public function create()
    {
        $banks = Bank::where('status', 1)->lists('name', 'id');
        return view('bankTransactions.create')->with('banks', $banks);
    }

    public function store(BankTransactionRequest $request)
    {
        try {

            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $time = time();
                $user = Auth::user();
                $year = CommonHelper::get_current_financial_year();
                $bankTransaction = new BankTransaction();

                // Transaction Entry
                $bankTransaction->bank_id = $inputs['bank_id'];
                $bankTransaction->transaction_type = $inputs['transaction_type'];
                $bankTransaction->amount = $inputs['amount'];
                $bankTransaction->transaction_date = $inputs['transaction_date'];
                $bankTransaction->created_by = $user->id;
                $bankTransaction->created_at = $time;
                $bankTransaction->save();

                if ($inputs['amount'] > 0) {
                    if ($inputs['transaction_type'] == 1) {
                        // Bank Balance Update
                        $bank = Bank::findOrfail($inputs['bank_id']);
                        $bank->balance += $inputs['amount'];
                        $bank->updated_by = $user->id;
                        $bank->updated_by = $time;
                        $bank->save();
                        // Update Workspace Ledger Bank
                        $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => $bank->account_code, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $workspaceLedger->balance += $inputs['amount'];
                        $workspaceLedger->updated_by = $user->id;
                        $workspaceLedger->updated_by = $time;
                        $workspaceLedger->save();
                        // Update Workspace Ledger Cash
                        $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => $bank->account_code, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $workspaceLedger->balance -= $inputs['amount'];
                        $workspaceLedger->updated_by = $user->id;
                        $workspaceLedger->updated_by = $time;
                        $workspaceLedger->save();
                        // General Journals Cash Credit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($inputs['transaction_date']);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.bank_deposit');
                        $generalJournal->reference_id = $bankTransaction->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 11000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['amount'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                        // Bank Account Debit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($inputs['transaction_date']);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.bank_deposit');
                        $generalJournal->reference_id = $bankTransaction->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = $bank->account_code;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['amount'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    } elseif ($inputs['transaction_type'] == 2) {
                        // Bank Balance Update
                        $bank = Bank::findOrfail($inputs['bank_id']);
                        $bank->balance -= $inputs['amount'];
                        $bank->updated_by = $user->id;
                        $bank->updated_by = $time;
                        $bank->save();
                        // Update Workspace Ledger Bank
                        $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => $bank->account_code, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $workspaceLedger->balance -= $inputs['amount'];
                        $workspaceLedger->updated_by = $user->id;
                        $workspaceLedger->updated_by = $time;
                        $workspaceLedger->save();
                        // Update Workspace Ledger Cash
                        $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 11000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $workspaceLedger->balance += $inputs['amount'];
                        $workspaceLedger->updated_by = $user->id;
                        $workspaceLedger->updated_by = $time;
                        $workspaceLedger->save();
                        // General Journals Cash Debit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($inputs['transaction_date']);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.bank_withdraw');
                        $generalJournal->reference_id = $bankTransaction->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = 11000;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['amount'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                        // Bank Account Credit
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = strtotime($inputs['transaction_date']);
                        $generalJournal->transaction_type = Config::get('common.transaction_type.bank_withdraw');
                        $generalJournal->reference_id = $bankTransaction->id;
                        $generalJournal->year = $year;
                        $generalJournal->account_code = $bank->account_code;
                        $generalJournal->workspace_id = $user->workspace_id;
                        $generalJournal->amount = $inputs['amount'];
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    } elseif ($inputs['transaction_type'] == 3) {
                        // Bank Balance Update
                        $bank = Bank::findOrfail($inputs['bank_id']);
                        $bank->balance += $inputs['amount'];
                        $bank->updated_by = $user->id;
                        $bank->updated_by = $time;
                        $bank->save();
                        // Update Workspace Ledger Bank
                        $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => $bank->account_code, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                        $workspaceLedger->balance += $inputs['amount'];
                        $workspaceLedger->updated_by = $user->id;
                        $workspaceLedger->updated_by = $time;
                        $workspaceLedger->save();
                    }
                }
            });
        } catch (\Exception $e) {
            dd($e);
            Session::flash('error_message', 'Failed to do Bank Transaction. Please try again!');
            return Redirect::back();
        }
        Session::flash('flash_message', 'Bank Transaction done successfully!');
        return redirect('bank_transactions');
    }

    public function edit($id = null)
    {
        $bank = bank::where(['id' => $id])->first();
        return view('banks.edit')->with('bank', $bank);
    }

    public function update(BankTransactionRequest $request, $id)
    {
        $inputs = $request->input();
        $inputs['updated_by'] = Auth::user()->id;
        $inputs['updated_at'] = time();

        unset($inputs['_method']);
        unset($inputs['_token']);
        Bank::where(['id' => $id])->update($inputs);

        Session::flash('flash_message', 'Bank updated successfully');
        return redirect('banks');
    }
}
