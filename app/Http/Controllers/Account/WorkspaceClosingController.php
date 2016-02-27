<?php

namespace App\Http\Controllers\Account;

use App\Http\Requests;
use App\Models\Adjustment;
use App\Models\ChartOfAccount;
use App\Models\WorkspaceLedger;
use App\Models\GeneralJournal;
use App\Models\AccountClosing;
use Carbon\Carbon;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdjustmentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class WorkspaceClosingController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        return view('workspaceClosing.index', compact('adjustments'));
    }

    public function create()
    {
        $accounts_from = ChartOfAccount::whereIn('code', Config::get('common.adjustment_account_from'))->lists('name', 'code');
        $accounts_to = ChartOfAccount::whereIn('code', Config::get('common.adjustment_account_to'))->lists('name', 'code');
        return view('adjustments.create', compact('accounts_from', 'accounts_to'));
    }

    public function store(Request $request)
    {
        try
        {
            DB::transaction(function () use ($request)
            {
                $workspace_id = Auth::user()->workspace_id;
                $levelZeros = ChartOfAccount::where(['parent'=>0, 'status'=>1])->select('id', 'name', 'code')->get();
                foreach($levelZeros as $levelZero)
                {
                    $workspaceData = WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'account_code'=>$levelZero->code,'balance_type'=>Config::get('common.balance_type_intermediate'), 'year'=>CommonHelper::get_current_financial_year()])->first();
                    $balance = isset($workspaceData->balance)?$workspaceData->balance:0;
                    $a[] = [$levelZero->id, $levelZero->code, '', $balance];
                }

                for($ci=0; isset($a[$ci][0]); $ci++)
                {
                    $nextLevels = ChartOfAccount::where(['parent'=> $a[$ci][0], 'status'=>1])->get(['code', 'id']);
                    foreach($nextLevels as $nextLevel)
                    {
                        $NextLevelWorkspaceData = WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'account_code'=>$nextLevel->code,'balance_type'=>Config::get('common.balance_type_intermediate'), 'year'=>CommonHelper::get_current_financial_year()])->first();
                        $nextLevelBalance = isset($NextLevelWorkspaceData->balance)?$NextLevelWorkspaceData->balance:0;
                        $a[] = [$nextLevel->id, $nextLevel->code, $ci, $nextLevelBalance];
                    }
                }

                $ci = sizeof($a)-1;

                for( ; $ci>=0; $ci--)
                {
                    if(isset($a[$a[$ci][2]][3]))
                    {
                        $a[$a[$ci][2]][3] += $a[$ci][3];
                    }

                    // Closing Balance
                    $workspaceLedger = New WorkspaceLedger;
                    $workspaceLedger->workspace_id = $workspace_id;
                    $workspaceLedger->year = CommonHelper::get_current_financial_year();
                    $workspaceLedger->account_code = $a[$ci][1];
                    $workspaceLedger->balance_type = Config::get('common.balance_type_closing');
                    $workspaceLedger->balance = $a[$ci][3];
                    $workspaceLedger->created_by = Auth::user()->id;
                    $workspaceLedger->created_at = time();
                    $workspaceLedger->save();
                    // Initial Balance Next Year
                    $workspaceLedger = New WorkspaceLedger;
                    $workspaceLedger->workspace_id = $workspace_id;
                    $workspaceLedger->year = CommonHelper::get_current_financial_year()+1;
                    $workspaceLedger->account_code = $a[$ci][1];
                    $workspaceLedger->balance_type = Config::get('common.balance_type_opening');
                    $workspaceLedger->balance = $a[$ci][3];
                    $workspaceLedger->created_by = Auth::user()->id;
                    $workspaceLedger->created_at = time();
                    $workspaceLedger->save();
                    // Intermediate Balance Next Year
                    $workspaceLedger = New WorkspaceLedger;
                    $workspaceLedger->workspace_id = $workspace_id;
                    $workspaceLedger->year = CommonHelper::get_current_financial_year()+1;
                    $workspaceLedger->account_code = $a[$ci][1];
                    $workspaceLedger->balance_type = Config::get('common.balance_type_intermediate');
                    $workspaceLedger->balance = $a[$ci][3];
                    $workspaceLedger->created_by = Auth::user()->id;
                    $workspaceLedger->created_at = time();
                    $workspaceLedger->save();
                }

                // Workspace Account Close Info
                $accountClosing = New AccountClosing;
                $accountClosing->workspace_id = $workspace_id;
                $accountClosing->year = CommonHelper::get_current_financial_year();
                $accountClosing->save();
            });
        }
        catch (\Exception $e)
        {
            Session()->flash('flash_message', 'Workspace Account Closing Not Done!');
            return redirect('workspace_closing');
        }

        Session()->flash('flash_message', 'Workspace Account Closed Successfully!');
        return redirect('workspace_closing');
    }
}
