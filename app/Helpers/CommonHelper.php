<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 13-Jan-16
 * Time: 4:45 PM
 */

namespace App\Helpers;

use DB;
use Illuminate\Support\Facades\Auth;


class CommonHelper
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
        }elseif($type==1)
        {
            return "";
        }

        return "";

    }

    public static function get_years()
    {
        $current = intval(date('Y'));
        $years = [];
        for($i=$current; $i<$current+5; $i++)
        {
            $years[] = $i;
        }
        return $years;
    }
}