<?php

namespace App\Http\Controllers\Account;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\AccountClosing;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use App\Models\RawStock;
use App\Models\Workspace;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;

class YearClosingController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        return view('yearClosing.index');
    }

    public function store(Request $request)
    {
        $yearClosingStatus = AccountClosing::where(['year' => CommonHelper::get_current_financial_year(), 'type' => 2])->lists('id');
        if (sizeof($yearClosingStatus) == 0) {
            $workspaces = Workspace::where('status', 1)->lists('id');
            $closedWorkspaces = AccountClosing::where(['year' => CommonHelper::get_current_financial_year(), 'type' => 1])->lists('workspace_id');

            if (sizeof($workspaces) == sizeof($closedWorkspaces)) {
                try {
                    DB::transaction(function () use ($request) {
                        $heads = ChartOfAccount::where('status', 1)->lists('code');
                        $currentYear = CommonHelper::get_current_financial_year();
                        foreach ($heads as $head) {
                            $headTotal = WorkspaceLedger::where(['account_code' => $head, 'year' => CommonHelper::get_current_financial_year(), 'balance_type' => Config::get('common.balance_type_closing')])->sum('balance');

                            // Closing Balance Set
                            $generalLedger = New GeneralLedger;
                            $generalLedger->year = $currentYear;
                            $generalLedger->account_code = $head;
                            $generalLedger->balance_type = Config::get('common.balance_type_closing');
                            $generalLedger->balance = $headTotal;
                            $generalLedger->created_by = Auth::user()->id;
                            $generalLedger->created_at = time();
                            $generalLedger->save();
                            // Opening Balance Set for Next Financial Year
                            $generalLedger = New GeneralLedger;
                            $generalLedger->year = CommonHelper::get_next_financial_year();
                            $generalLedger->account_code = $head;
                            $generalLedger->balance_type = Config::get('common.balance_type_opening');
                            $generalLedger->balance = $headTotal;
                            $generalLedger->created_by = Auth::user()->id;
                            $generalLedger->created_at = time();
                            $generalLedger->save();
                        }

                        // Account Closing table Impact
                        $accountClosing = New AccountClosing;
                        $accountClosing->type = 2; // Year Closing type=2 and Workspace Closing type=1;
                        $accountClosing->year = $currentYear;
                        $accountClosing->save();

                        // Raw Stock Table Impact
                        $rawMaterials = RawStock::where(['year' => CommonHelper::get_current_financial_year(), 'stock_type' => Config::get('common.balance_type_intermediate')])->get();
                        foreach ($rawMaterials as $rawMaterial) {
                            // Current Year Opening Balance
                            $rawStock = New RawStock;
                            $rawStock->year = $currentYear;
                            $rawStock->stock_type = Config::get('common.balance_type_closing');
                            $rawStock->material_id = $rawMaterial->material_id;
                            $rawStock->quantity = $rawMaterial->quantity;
                            $rawStock->created_by = Auth::user()->id;
                            $rawStock->created_at = time();
                            // Next Year Opening Balance
                            $rawStock = New RawStock;
                            $rawStock->year = CommonHelper::get_next_financial_year();
                            $rawStock->stock_type = Config::get('common.balance_type_opening');
                            $rawStock->material_id = $rawMaterial->material_id;
                            $rawStock->quantity = $rawMaterial->quantity;
                            $rawStock->created_by = Auth::user()->id;
                            $rawStock->created_at = time();
                            // Next Year Intermediate Balance
                            $rawStock = New RawStock;
                            $rawStock->year = CommonHelper::get_next_financial_year();
                            $rawStock->stock_type = Config::get('common.balance_type_intermediate');
                            $rawStock->material_id = $rawMaterial->material_id;
                            $rawStock->quantity = $rawMaterial->quantity;
                            $rawStock->created_by = Auth::user()->id;
                            $rawStock->created_at = time();
                        }

                        // Current Year Data Fetch
                        $existingYearDetail = DB::table('financial_years')->where('year', $currentYear)->first();
                        // Current Year Inactive
                        DB::table('financial_years')->where('year', $currentYear)->update(['status' => 0]);
                        // New Year Insert
                        DB::table('financial_years')->insert(
                            [
                                'year' => CommonHelper::get_next_financial_year(),
                                'start_date'=>strtotime(date("Y-m-d", $existingYearDetail->start_date) . " + 1 year"),
                                'end_date'=>strtotime(date("Y-m-d", $existingYearDetail->end_date) . " + 1 year"),
                                'created_by'=>Auth::user()->id,
                                'created_at'=>time()
                            ]
                        );
                    });
                } catch (\Exception $e) {
                    Session()->flash('error_message', 'Year Closing Not Done!');
                    return redirect('year_closing');
                }

                Session()->flash('flash_message', 'Year Closed And New Year Opened Successfully!');
                return redirect('year_closing');
            } else {
                Session()->flash('warning_message', 'Warning: All Workspace Account Not Closed Yet!');
                return redirect('year_closing');
            }
        } else {
            Session()->flash('warning_message', 'Warning: Year Closing Done For This Year!');
            return redirect('year_closing');
        }
    }
}
