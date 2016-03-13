<?php

namespace App\Http\Controllers\Account;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\AccountClosing;
use App\Models\FinancialYear;
use App\Models\GeneralJournal;
use App\Models\GeneralLedger;
use App\Models\RawStock;
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
        try {
            DB::transaction(function () use ($request) {
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
                            // Delete Workspace Closing Balance Data of Current Year
                            WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'balance_type'=>Config::get('common.balance_type_closing'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                            // Delete Next Year Opening Balance Data
                            WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'balance_type'=>Config::get('common.balance_type_opening'), 'year'=>CommonHelper::get_next_financial_year()])->delete();
                            // Delete Next Year Intermediate Balance Data
                            WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'balance_type'=>Config::get('common.balance_type_intermediate'), 'year'=>CommonHelper::get_next_financial_year()])->delete();
                            //  Delete Stock Table Current Year Closing Balance Data
                            Stock::where(['workspace_id'=>$workspace_id, 'stock_type'=>Config::get('common.balance_type_closing'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                            // Delete Next Year Opening Balance Data
                            Stock::where(['workspace_id'=>$workspace_id, 'stock_type'=>Config::get('common.balance_type_opening'), 'year'=>CommonHelper::get_next_financial_year()])->delete();
                            // Delete Stock Table Next Year Intermediate Balance Data
                            Stock::where(['workspace_id'=>$workspace_id, 'stock_type'=>Config::get('common.balance_type_intermediate'), 'year'=>CommonHelper::get_next_financial_year()])->delete();
                            // Delete Account Closing Data
                            AccountClosing::where(['workspace_id'=>$workspace_id, 'year'=>CommonHelper::get_current_financial_year(), 'type'=>1])->delete();
                        }
                        else
                        {
                            Session()->flash('warning_message', 'Alert: Time Over!');
                            throw new \Exception('error');
                        }
                    }
                    else
                    {
                        Session()->flash('warning_message', 'Alert: Workspace Not Closed Yet!');
                        throw new \Exception('error');
                    }
                }
                else
                {
                    // Total System is being rolled back to the previous year
                    // Delete General Ledger Current Year Closing Data
                    GeneralLedger::where(['balance_type'=>Config::get('common.balance_type_closing'), 'year'=>CommonHelper::get_previous_financial_year()])->delete();
                    // Delete General Ledger Next Year Opening Data
                    GeneralLedger::where(['balance_type'=>Config::get('common.balance_type_opening'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                    // Delete General Journal Table Current Year Data
                    GeneralJournal::where(['year'=>CommonHelper::get_current_financial_year()])->delete();
                    // Delete Account Closing Data
                    AccountClosing::where(['year'=>CommonHelper::get_previous_financial_year(), 'type'=>2])->delete();
                    //  Delete Raw Stock Table Previous Year Closing Balance Data
                    RawStock::where(['stock_type'=>Config::get('common.balance_type_closing'), 'year'=>CommonHelper::get_previous_financial_year()])->delete();
                    //  Delete Raw Stock Table Current Year Opening Balance Data
                    RawStock::where(['stock_type'=>Config::get('common.balance_type_opening'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                    //  Delete Raw Stock Table Current Year Intermediate Balance Data
                    RawStock::where(['stock_type'=>Config::get('common.balance_type_intermediate'), 'year'=>CommonHelper::get_current_financial_year()])->delete();

                    // Workspaces Rollback
                    $workspaces = Workspace::where('status', '=', 1)->get();
                    foreach($workspaces as $workspace)
                    {
                        $workspace_id = $workspace->id;
                        // Delete Workspace Closing Balance Data of Current Year
                        WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'balance_type'=>Config::get('common.balance_type_closing'), 'year'=>CommonHelper::get_previous_financial_year()])->delete();
                        // Delete Next Year Opening Balance Data
                        WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'balance_type'=>Config::get('common.balance_type_opening'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                        // Delete Next Year Intermediate Balance Data
                        WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'balance_type'=>Config::get('common.balance_type_intermediate'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                        //  Delete Stock Table Current Year Closing Balance Data
                        Stock::where(['workspace_id'=>$workspace_id, 'stock_type'=>Config::get('common.balance_type_closing'), 'year'=>CommonHelper::get_previous_financial_year()])->delete();
                        // Delete Next Year Opening Balance Data
                        Stock::where(['workspace_id'=>$workspace_id, 'stock_type'=>Config::get('common.balance_type_opening'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                        // Delete Stock Table Next Year Intermediate Balance Data
                        Stock::where(['workspace_id'=>$workspace_id, 'stock_type'=>Config::get('common.balance_type_intermediate'), 'year'=>CommonHelper::get_current_financial_year()])->delete();
                        // Delete Account Closing Data
                        AccountClosing::where(['workspace_id'=>$workspace_id, 'year'=>CommonHelper::get_previous_financial_year(), 'type'=>1])->delete();
                        // Fiscal Year Table Operations
                        $previous = CommonHelper::get_previous_financial_year();
                        $current = CommonHelper::get_current_financial_year();
                        // Previous Year Activate
                        DB::table('financial_years')->where('year', $previous)->update(['status' => 1]);
                        // Delete Current Financial Year
                        FinancialYear::where(['year'=>$current])->delete();
                    }
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Rollback not done!');
            return redirect('rollback');
        }

        Session()->flash('flash_message', 'Rollback Successfully done!');
        return redirect('rollback');
    }
}
