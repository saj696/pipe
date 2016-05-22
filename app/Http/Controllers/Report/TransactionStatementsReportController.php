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

class TransactionStatementsReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        return view('reports.transactionStatements.index')->with(compact('workspace'));
    }

    public function getReport(Request $request)
    {
        $currentYear = CommonHelper::get_current_financial_year();
        $type = $request->statement_for;
        $person_id = $request->person_id;

        $balanceDueBeforeCurrentYear = CommonHelper::customer_balance_due_before_date($person_id, $type, strtotime('01-01-'.$currentYear));

        // Statement Codes
        $salesOrder = DB::table('sales_order')
            ->where(['customer_type' => $type, 'customer_id' => $person_id])
            ->select('id', 'date', DB::raw('SUM(total) as sum_total'), DB::raw('SUM(transport_cost) as sum_transport_cost'), DB::raw('SUM(labour_cost) as sum_labour_cost'), DB::raw('SUM(paid) as sum_paid'))
            ->groupBy('date')
            ->where('date', '>', strtotime('01-01-'.$currentYear))
            ->get();

        $arrangedSalesOrder = [];
        foreach($salesOrder as $order)
        {
            $arrangedSalesOrder[$order->date]['Sales Order']['sum_total'] = $order->sum_total+$order->sum_transport_cost+$order->sum_labour_cost;
            $arrangedSalesOrder[$order->date]['Sales Order']['sum_paid'] = $order->sum_paid;
            $arrangedSalesOrder[$order->date]['Sales Order']['memo_no'] = $order->id;
        }

        $salesReturn = DB::table('sales_return')
            ->where(['customer_type' => $type, 'customer_id' => $person_id])
            ->select('id', 'date', DB::raw('SUM(total_amount) as sum_total_amount'), DB::raw('SUM(due_paid) as sum_due_paid'))
            ->groupBy('date')
            ->where('date', '>', strtotime('01-01-'.$currentYear))
            ->get();

        $arrangedSalesReturn = [];
        foreach($salesReturn as $return)
        {
            $arrangedSalesReturn[$return->date]['Sales Return']['sum_total'] = $return->sum_total_amount;
            $arrangedSalesReturn[$return->date]['Sales Return']['sum_paid'] = $return->sum_due_paid;
            $arrangedSalesReturn[$return->date]['Sales Return']['memo_no'] = $return->id;
        }

        $defects = DB::table('defects')
            ->where(['customer_type' => $type, 'customer_id' => $person_id])
            ->select('id', 'date', DB::raw('SUM(cash) as sum_cash'), DB::raw('SUM(replacement) as sum_replacement'), DB::raw('SUM(total) as sum_total'), DB::raw('SUM(due) as sum_due'))
            ->groupBy('date')
            ->where('date', '>', strtotime('01-01-'.$currentYear))
            ->get();

        $arrangedDefects = [];
        foreach($defects as $defect)
        {
            $arrangedDefects[$defect->date]['Defects']['sum_cash'] = $defect->sum_cash;
            $arrangedDefects[$defect->date]['Defects']['sum_replacement'] = $defect->sum_replacement;
            $arrangedDefects[$defect->date]['Defects']['sum_total'] = $defect->sum_total;
            $arrangedDefects[$defect->date]['Defects']['sum_due'] = $defect->sum_due;
            $arrangedDefects[$defect->date]['Defects']['memo_no'] = $defect->id;
        }

        $receivePayments = DB::table('payments')
            ->where(['from_whom_type' => $type, 'from_whom' => $person_id])
            ->select('id', 'voucher_no', 'date', DB::raw('SUM(amount) as sum_amount'), DB::raw('SUM(total_amount) as sum_total_amount'))
            ->groupBy('date')
            ->where('date', '>', strtotime('01-01-'.$currentYear))
            ->get();

        $arrangedReceivePayments = [];
        foreach($receivePayments as $receivePayment)
        {
            $arrangedReceivePayments[$receivePayment->date]['Receive Payment']['sum_paid'] = $receivePayment->sum_amount;
            $arrangedReceivePayments[$receivePayment->date]['Receive Payment']['sum_total'] = $receivePayment->sum_total_amount;
            $arrangedReceivePayments[$receivePayment->date]['Receive Payment']['memo_no'] = $receivePayment->id;
            $arrangedReceivePayments[$receivePayment->date]['Receive Payment']['voucher_no'] = $receivePayment->id;
        }

        $makePayments = DB::table('payments')
            ->where(['to_whom_type' => $type, 'to_whom' => $person_id])
            ->select('id', 'voucher_no', 'date', DB::raw('SUM(amount) as sum_amount'), DB::raw('SUM(total_amount) as sum_total_amount'))
            ->groupBy('date')
            ->where('date', '>', strtotime('01-01-'.$currentYear))
            ->get();

        $arrangedMakePayments = [];
        foreach($makePayments as $makePayment)
        {
            $arrangedMakePayments[$makePayment->date]['Make Payment']['sum_paid'] = $makePayment->sum_amount;
            $arrangedMakePayments[$makePayment->date]['Make Payment']['sum_total'] = $makePayment->sum_total_amount;
            $arrangedMakePayments[$makePayment->date]['Make Payment']['memo_no'] = $makePayment->id;
            $arrangedMakePayments[$makePayment->date]['Make Payment']['voucher_no'] = $makePayment->id;
        }

        $newArray = [];
        foreach($arrangedSalesOrder as $key=>$arr)
        {
            if(isset($arrangedSalesReturn[$key])):
                $newArray[$key] = array_merge_recursive($arrangedSalesReturn[$key],$arr);
            else:
                $newArray[$key] = $arr;
            endif;
        }

        $newArray2 = [];
        foreach($newArray as $key=>$arr)
        {
            if(isset($arrangedDefects[$key])):
                $newArray2[$key] = array_merge_recursive($arrangedDefects[$key],$arr);
            else:
                $newArray2[$key] = $arr;
            endif;
        }

        $newArray3 = [];
        foreach($newArray2 as $key=>$arr)
        {
            if(isset($arrangedReceivePayments[$key])):
                $newArray3[$key] = array_merge_recursive($arrangedReceivePayments[$key],$arr);
            else:
                $newArray3[$key] = $arr;
            endif;
        }

        $newArray4 = [];
        foreach($newArray3 as $key=>$arr)
        {
            if(isset($arrangedMakePayments[$key])):
                $newArray4[$key] = array_merge_recursive($arrangedMakePayments[$key],$arr);
            else:
                $newArray4[$key] = $arr;
            endif;
        }

        $statements = json_decode(json_encode($newArray4), true);
        $balanceDueBeforeCurrentYear = json_decode(json_encode($balanceDueBeforeCurrentYear), true);

        $ajaxView = view('reports.transactionStatements.view', compact('statements', 'balanceDueBeforeCurrentYear', 'type', 'person_id'))->render();
        return response()->json($ajaxView);
    }

}
