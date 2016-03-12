<?php

namespace App\Http\Controllers\Account;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\AccountClosing;
use App\Models\ChartOfAccount;
use App\Models\GeneralJournal;
use App\Models\Workspace;
use App\Models\Stock;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class RollbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $workspaces = Workspace::where('status', '=', 1)->lists('name', 'id');
        return view('rollback.index', compact('workspaces'));
    }

    public function store(Request $request)
    {
        $workspace_id = $request->workspace_id;
        if($workspace_id>0)
        {
            $currentYear = CommonHelper::get_current_financial_year();
            $closingStatus = DB::table('account_closings')->where(['year'=> $currentYear, 'workspace_id'=>$workspace_id])->value('status');
            $existingYearDetail = DB::table('financial_years')->where('year', $currentYear)->first();

            if ($closingStatus==1)
            {
                if($existingYearDetail->end_date > strtotime(date('Y-m-d')))
                {
                    // Workspace Ledger Operations
                    // Delete Workspace Closing Balance Data of Current Year
                    WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'balance_type'=>Config::get('common.balance_type_closing'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                    // Delete Next Year Opening Balance Data
                    WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'balance_type'=>Config::get('common.balance_type_opening'), 'year'=>CommonHelper::get_next_financial_year()])->delete();
                    // Delete Next Year Intermediate Balance Data
                    WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'balance_type'=>Config::get('common.balance_type_intermediate'), 'year'=>CommonHelper::get_next_financial_year()])->delete();
                    // Stocks Table Operations
                    //  Delete Stock Table Current Year Closing Balance Data
                    Stock::where(['workspace_id'=>$workspace_id, 'stock_type'=>Config::get('common.balance_type_closing'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                    // Delete Next Year Opening Balance Data
                    Stock::where(['workspace_id'=>$workspace_id, 'stock_type'=>Config::get('common.balance_type_opening'), 'year'=>CommonHelper::get_next_financial_year()])->delete();
                    // Delete Stock Table Next Year Intermediate Balance Data
                    Stock::where(['workspace_id'=>$workspace_id, 'stock_type'=>Config::get('common.balance_type_intermediate'), 'year'=>CommonHelper::get_next_financial_year()])->delete();
                    // Delete Account Closing Data
                    AccountClosing::where(['workspace_id'=>$workspace_id, 'year'=>CommonHelper::get_current_financial_year(), 'type'=>1])->delete();
                }
            }
        }
        else
        {

        }

        try {
            DB::transaction(function () use ($request) {
                $currentYear = CommonHelper::get_current_financial_year();
                $workspace_id = Auth::user()->workspace_id;
                $adjustment = New Adjustment;
                $adjustment->year = $currentYear;
                $adjustment->account_from = $request->account_from;
                $adjustment->amount = $request->amount;

                if ($request->account_from == 25000) {
                    $adjustment->account_to = 14000;
                } elseif ($request->account_from == 27000) {
                    $adjustment->account_to = 13000;
                }

                $adjustment->created_by = Auth::user()->id;
                $adjustment->created_at = time();
                $adjustment->save();

                if ($request->account_from == 25000) {
                    // Workspace_id hardcoded for head office
                    // Workspace Ledger Purchase Credit(-)
                    $purchaseWorkspaceData = WorkspaceLedger::where(['workspace_id' => 1, 'account_code' => 25000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $purchaseWorkspaceData->balance = $purchaseWorkspaceData->balance - $request->amount;
                    $purchaseWorkspaceData->update();
                    // Workspace Ledger Inventory Raw Material Account Debit(+)
                    $assetWorkspaceData = WorkspaceLedger::where(['workspace_id' => 1, 'account_code' => 14000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $assetWorkspaceData->balance += $request->amount;
                    $assetWorkspaceData->update();
                    // General Journals Insert
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = time();
                    $generalJournal->transaction_type = Config::get('common.transaction_type.purchase');
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = 14000;
                    $generalJournal->workspace_id = 1; // Hard Coded 1 For Head Office (Raw Material)
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                } elseif ($request->account_from == 27000) {
                    // Workspace Ledger Office Supply Credit(-)
                    $officeWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => 27000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $officeWorkspaceData->balance = $officeWorkspaceData->balance - $request->amount;
                    $officeWorkspaceData->update();
                    // Workspace Ledger Inventory Office Supplies Account Debit(+)
                    $assetWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => 13000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])->first();
                    $assetWorkspaceData->balance += $request->amount;
                    $assetWorkspaceData->update();
                    // General Journals Insert
                    $generalJournal = New GeneralJournal;
                    $generalJournal->date = time();
                    $generalJournal->transaction_type = Config::get('common.transaction_type.office_supply');
                    $generalJournal->year = $currentYear;
                    $generalJournal->account_code = 13000;
                    $generalJournal->workspace_id = $workspace_id;
                    $generalJournal->amount = $request->amount;
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = Auth::user()->id;
                    $generalJournal->created_at = time();
                    $generalJournal->save();
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Adjustment not done!');
            return redirect('adjustments');
        }

        Session()->flash('flash_message', 'Adjustment has been done!');
        return redirect('adjustments');
    }
}
