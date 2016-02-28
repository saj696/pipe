<?php

namespace App\Http\Controllers\Account;

use App\Http\Requests;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use App\Models\Workspace;
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

class YearClosingController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        return view('yearClosing.index');
    }

    public function store(Request $request)
    {
        $workspaces = Workspace::where('status', 1)->lists('id');
        $closedWorkspaces = AccountClosing::where(['year'=>CommonHelper::get_current_financial_year(), 'type'=>1])->lists('workspace_id');

        if(sizeof($workspaces)==sizeof($closedWorkspaces))
        {
            try
            {
                DB::transaction(function () use ($request)
                {
                    $heads = ChartOfAccount::where('status', 1)->lists('code');
                    foreach($heads as $head)
                    {
                        $headTotal = WorkspaceLedger::where(['account_code'=>$head, 'year'=>CommonHelper::get_current_financial_year(), 'balance_type'=>Config::get('common.balance_type_closing')])->sum('balance');

                        // Closing Balance Set
                        $generalLedger = New GeneralLedger;
                        $generalLedger->year = CommonHelper::get_current_financial_year();
                        $generalLedger->account_code = $head;
                        $generalLedger->balance_type = Config::get('common.balance_type_closing');
                        $generalLedger->balance = $headTotal;
                        $generalLedger->created_by = Auth::user()->id;
                        $generalLedger->created_at = time();
                        $generalLedger->save();
                        // Opening Balance Set for Next Financial Year
                        $generalLedger = New GeneralLedger;
                        $generalLedger->year = CommonHelper::get_current_financial_year()+1;
                        $generalLedger->account_code = $head;
                        $generalLedger->balance_type = Config::get('common.balance_type_opening');
                        $generalLedger->balance = $headTotal;
                        $generalLedger->created_by = Auth::user()->id;
                        $generalLedger->created_at = time();
                        $generalLedger->save();
                    }

                    // Next Fiscal Year Entry
                    DB::table('financial_years')->where('year', CommonHelper::get_current_financial_year())->update(['status' => 0]);
                    DB::table('financial_years')->insert(
                        ['year' => CommonHelper::get_current_financial_year()+1]
                    );
                });
            }
            catch (\Exception $e)
            {
                Session()->flash('error_message', 'Year Closing Not Done!');
                return redirect('year_closing');
            }

            Session()->flash('flash_message', 'Year Closed And New Year Opened Successfully!');
            return redirect('year_closing');
        }
        else
        {
            Session()->flash('warning_message', 'All Workspace Account Not Closed Yet!');
            return redirect('year_closing');
        }
    }
}
