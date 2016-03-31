<?php

namespace App\Http\Controllers\Report;

use App\Helpers\CommonHelper;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\Supplier;
use App\Models\Workspace;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DailyCashFlowReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        return view('reports.cashFlow.index')->with(compact('workspace'));
    }

    public function getReport(Request $request)
    {
        $customers = Customer::where('status', 1)->lists('name', 'id');
        $employees = Employee::where('status', 1)->lists('name', 'id');
        $suppliers = Supplier::where('status', 1)->lists('company_name', 'id');

        $sales = DB::table('general_journals')
            ->select('general_journals.*', 'chart_of_accounts.name', 'sales_order.customer_id', 'sales_order.customer_type')
            ->join('chart_of_accounts', 'chart_of_accounts.code', '=', 'general_journals.account_code')
            ->join('sales_order', 'sales_order.id', '=', 'general_journals.reference_id')
            ->where(['transaction_type'=>Config::get('common.transaction_type.sales'),'account_code'=>31000,'year'=>CommonHelper::get_current_financial_year(), 'general_journals.status'=>1])
            ->where('general_journals.date', '=', strtotime(date('d-m-Y')))
            ->get();

        $salesReturns = DB::table('general_journals')
            ->select('general_journals.*', 'chart_of_accounts.name', 'sales_order.customer_id', 'sales_order.customer_type')
            ->join('chart_of_accounts', 'chart_of_accounts.code', '=', 'general_journals.account_code')
            ->join('sales_order', 'sales_order.id', '=', 'general_journals.reference_id')
            ->where(['transaction_type'=>Config::get('common.transaction_type.sales_return'),'account_code'=>32000,'year'=>CommonHelper::get_current_financial_year(), 'general_journals.status'=>1])
            ->where('general_journals.date', '=', strtotime(date('d-m-Y')))
            ->get();

        $expenses = DB::table('general_journals')
            ->select('general_journals.*', 'chart_of_accounts.name')
            ->join('chart_of_accounts', 'chart_of_accounts.code', '=', 'general_journals.account_code')
            ->where('general_journals.account_code', 'like', '2%')
            ->where('general_journals.date', '=', strtotime(date('d-m-Y')))
            ->get();

        $sales = json_decode(json_encode($sales), true);
        $salesReturns = json_decode(json_encode($salesReturns), true);
        $expenses = json_decode(json_encode($expenses), true);

        $ajaxView = view('reports.cashFlow.view', compact('sales', 'salesReturns', 'expenses', 'customers', 'employees', 'suppliers'))->render();
        return response()->json($ajaxView);
    }

}
