<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\BankRequest;
use App\Models\Bank;
use App\Models\ChartOfAccount;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\WorkspaceLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class BanksController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $banks = DB::table('banks')
            ->select('*')
            ->where(['status' => 1])
            ->paginate(Config::get('common.pagination'));

        return view('banks.index')->with('banks', $banks);
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(BankRequest $request)
    {
        try {

            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $time = time();
                $user = Auth::user();
                $year = CommonHelper::get_current_financial_year();
                $bank = new Bank();
                $chartOfAccount = new ChartOfAccount();
                $workspaceLedgerData = new WorkspaceLedger();

                // Bank Entry
                $bank->name = $inputs['name'];
                $bank->account_name = $inputs['account_name'];
                $bank->account_no = $inputs['account_no'];
                $bank->account_director = $inputs['account_director'];
                $bank->account_type = $inputs['account_type'];
                $bank->start_date = $inputs['start_date'];
                $bank->balance = $inputs['opening_balance'];
                $bank->account_code = $inputs['account_code'];
                $bank->created_by = $user->id;
                $bank->created_at = $time;
                $bank->save();

                // Account Head Entry
                $chartOfAccount->parent = 1; // Asset
                $chartOfAccount->name = $inputs['name'];
                $chartOfAccount->code = $inputs['account_code'];
                $chartOfAccount->save();

                // Workspace Ledger Account Head Data (Opening)
                $workspaceLedgerData->workspace_id = $user->workspace_id;
                $workspaceLedgerData->year = $year;
                $workspaceLedgerData->account_code = $inputs['account_code'];
                $workspaceLedgerData->balance_type = Config::get('common.balance_type_opening');
                $workspaceLedgerData->balance = 0;
                $workspaceLedgerData->created_by = $user->id;
                $workspaceLedgerData->created_at = $time;
                $workspaceLedgerData->save();
                // Workspace Ledger Account Head Data (Intermediate)
                $workspaceLedgerData = new WorkspaceLedger();
                $workspaceLedgerData->workspace_id = $user->workspace_id;
                $workspaceLedgerData->year = $year;
                $workspaceLedgerData->account_code = $inputs['account_code'];
                $workspaceLedgerData->balance_type = Config::get('common.balance_type_intermediate');
                $workspaceLedgerData->balance = 0;
                $workspaceLedgerData->created_by = $user->id;
                $workspaceLedgerData->created_at = $time;
                $workspaceLedgerData->save();

                if ($inputs['opening_balance']>0) {
                    // Update Workspace Ledger
                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => $inputs['account_code'], 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                    $workspaceLedger->balance += $inputs['opening_balance'];
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_by = $time;
                    $workspaceLedger->save();
                }
            });
        } catch (\Exception $e) {
            dd($e);
            Session::flash('error_message', 'Failed to create Bank. Please try again!');
            return Redirect::back();
        }
        Session::flash('flash_message', 'Bank created successfully!');
        return redirect('banks');
    }

    public function edit($id = null)
    {
        $bank = bank::where(['id' => $id])->first();
        return view('banks.edit')->with('bank', $bank);
    }

    public function update(BankRequest $request, $id)
    {
        $inputs = $request->input();
        $inputs['updated_by'] = Auth::user()->id;
        $inputs['updated_at'] = time();

        unset($inputs['opening_balance']);
        unset($inputs['_method']);
        unset($inputs['_token']);
        Bank::where(['id' => $id])->update($inputs);

        Session::flash('flash_message', 'Bank updated successfully');
        return redirect('banks');
    }
}
