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
            return "";
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

}