<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 13-Jan-16
 * Time: 4:45 PM
 */

namespace App\Helpers;

use App\Models\SalesDeliveryDetail;
use DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade;


class CommonHelper extends Facade
{
    /* Get Customer name for Sales Order */
    public static function getCustomerName($id, $type)
    {
        if ($type == 3) {
            $customer = DB::table('customer')
                ->select('name')
                ->where('id', $id)
                ->where('status', 1)
                ->first();
            return $customer->name;
        } elseif ($type == 2) {
            $customer = DB::table('suppliers')
                ->select('company_name')
                ->where('id', $id)
                ->where('status', 1)
                ->first();
            return $customer->company_name;
        } elseif ($type == 1) {
            $customer= DB::table('employees')
                ->select('name')
                ->where('id',$id)
                ->where('status',1)
                ->first();

            return $customer->name;
        }

        return "";

    }

    public static function get_years()
    {
        $current = intval(date('Y'));
        $years = [];
        for ($i = $current; $i < $current + 5; $i++) {
            $years[] = $i;
        }
        return $years;
    }

    public static function get_current_financial_year()
    {
        $year = DB::table('financial_years')->where('status', '=', 1)->first();
        return $year->year;
    }

    public static function get_delivered_quantity($sales_order_id, $product_id)
    {
        $result = SalesDeliveryDetail::where(['sales_order_id' => $sales_order_id, 'product_id' => $product_id])->first();
        return $result->delivered_quantity;
    }

    public static function get_revised_balance_for_contra($array)
    {
        $totalNormal = 0;
        $totalContra = 0;
        $gross = 0;

        if(sizeof($array)>0)
        {
            foreach($array as $data)
            {
                if($data->contra_status==0)
                {
                    $totalNormal += $data->sum_amount;
                }
                elseif($data->contra_status==1)
                {
                    $totalContra += $data->sum_amount;
                }
            }
            $gross = $totalNormal - $totalContra;
        }

        return $gross;
    }

    public static function get_next_financial_year()
    {
        $year = DB::table('financial_years')->where('status', '=', 1)->first();
        $exploded = explode('-', $year->year);
        if(is_array($exploded) && sizeof($exploded)>1)
        {
            $yearStr = (intval($exploded[0])+1).'-'.(intval($exploded[1])+1);
        }
        else
        {
            $yearStr = intval($year->year)+1;
        }

        return $yearStr;
    }

    public static function get_previous_financial_year()
    {
        $year = DB::table('financial_years')->where('status', '=', 1)->first();
        $exploded = explode('-', $year->year);
        if(is_array($exploded) && sizeof($exploded)>1)
        {
            $yearStr = (intval($exploded[0])-1).'-'.(intval($exploded[1])-1);
        }
        else
        {
            $yearStr = intval($year->year)-1;
        }

        return $yearStr;
    }

    //Get current stock quantity by product_id from stocks table
    public static function get_current_stock($product_id)
    {
       $stock=DB::table('stocks')
           ->where('product_id','=',$product_id)
           ->where('year','=', self::get_current_financial_year())
           ->where('stock_type','=',Config::get('common.balance_type_intermediate'))
           ->first();

        return $stock->quantity;
    }

    public static function customer_balance_due_before_date($person_id, $type, $date)
    {
        $salesOrder = DB::table('sales_order')
            ->where(['customer_type' => $type, 'customer_id' => $person_id])
            ->select(DB::raw('SUM(total) as sum_total'), DB::raw('SUM(transport_cost) as sum_transport_cost'), DB::raw('SUM(labour_cost) as sum_labour_cost'), DB::raw('SUM(paid) as sum_paid'))
            ->where('date', '<', $date)
            ->first();
        $salesReturn = DB::table('sales_return')
            ->where(['customer_type' => $type, 'customer_id' => $person_id])
            ->select(DB::raw('SUM(total_amount) as sum_total_amount'), DB::raw('SUM(due_paid) as sum_due_paid'))
            ->where('date', '<', $date)
            ->first();
        $defect = DB::table('defects')
            ->where(['customer_type' => $type, 'customer_id' => $person_id])
            ->select(DB::raw('SUM(cash) as sum_cash'), DB::raw('SUM(replacement) as sum_replacement'), DB::raw('SUM(total) as sum_total'))
            ->where('date', '<', $date)
            ->first();
        $receivedPayments = DB::table('payments')
            ->where(['from_whom_type' => $type, 'from_whom' => $person_id])
            ->select(DB::raw('SUM(amount) as sum_amount'))
            ->where('date', '<', $date)
            ->first();
        $makePayments = DB::table('payments')
            ->where(['from_whom_type' => $type, 'from_whom' => $person_id, 'account_code' => 12200])
            ->select(DB::raw('SUM(amount) as sum_amount'))
            ->where('date', '<', $date)
            ->first();

        $paidValue = $salesOrder->sum_total + $salesOrder->sum_transport_cost + $salesOrder->sum_labour_cost + $defect->sum_replacement + $defect->sum_cash + $makePayments->sum_amount + $salesReturn->sum_total_amount - $salesReturn->sum_due_paid;
        $receivedValue = $salesOrder->sum_paid + $salesReturn->sum_total_amount + $defect->sum_total + $receivedPayments->sum_amount;

        $arr = [];
        if($paidValue > $receivedValue):
            $arr['balance'] = 0;
            $arr['due'] = $paidValue - $receivedValue;
        elseif($receivedValue > $paidValue):
            $arr['balance'] = $receivedValue - $paidValue;
            $arr['due'] = 0;
        else:
            $arr['balance'] = 0;
            $arr['due'] = 0;
        endif;
        return $arr;
    }

    public static function customer_balance_due_after_date($person_id, $type, $date)
    {
        $salesOrder = DB::table('sales_order')
            ->where(['customer_type' => $type, 'customer_id' => $person_id])
            ->select(DB::raw('SUM(total) as sum_total'), DB::raw('SUM(transport_cost) as sum_transport_cost'), DB::raw('SUM(labour_cost) as sum_labour_cost'), DB::raw('SUM(paid) as sum_paid'))
            ->where('date', '<=', $date)
            ->first();
        $salesReturn = DB::table('sales_return')
            ->where(['customer_type' => $type, 'customer_id' => $person_id])
            ->select(DB::raw('SUM(total_amount) as sum_total_amount'), DB::raw('SUM(due_paid) as sum_due_paid'))
            ->where('date', '<=', $date)
            ->first();
        $defect = DB::table('defects')
            ->where(['customer_type' => $type, 'customer_id' => $person_id])
            ->select(DB::raw('SUM(cash) as sum_cash'), DB::raw('SUM(replacement) as sum_replacement'), DB::raw('SUM(total) as sum_total'))
            ->where('date', '<=', $date)
            ->first();
        $receivedPayments = DB::table('payments')
            ->where(['from_whom_type' => $type, 'from_whom' => $person_id])
            ->select(DB::raw('SUM(amount) as sum_amount'))
            ->where('date', '<=', $date)
            ->first();
        $makePayments = DB::table('payments')
            ->where(['from_whom_type' => $type, 'from_whom' => $person_id, 'account_code' => 12200])
            ->select(DB::raw('SUM(amount) as sum_amount'))
            ->where('date', '<=', $date)
            ->first();

        $paidValue = $salesOrder->sum_total + $salesOrder->sum_transport_cost + $salesOrder->sum_labour_cost + $defect->sum_replacement + $defect->sum_cash + $makePayments->sum_amount + $salesReturn->sum_total_amount - $salesReturn->sum_due_paid;
        $receivedValue = $salesOrder->sum_paid + $salesReturn->sum_total_amount + $defect->sum_total + $receivedPayments->sum_amount;

        $arr = [];
        if($paidValue > $receivedValue):
            $arr['balance'] = 0;
            $arr['due'] = $paidValue - $receivedValue;
        elseif($receivedValue > $paidValue):
            $arr['balance'] = $receivedValue - $paidValue;
            $arr['due'] = 0;
        else:
            $arr['balance'] = 0;
            $arr['due'] = 0;
        endif;
        return $arr;
    }
}