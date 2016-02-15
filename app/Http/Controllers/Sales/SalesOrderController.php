<?php

namespace App\Http\Controllers\Sales;

use App\Models\Customer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SalesOrderController extends Controller
{
    public function index()
    {


    }


    public function create()
    {
        $customers=Customer::where('status',1)->lists('name','id');
        return view('sales.salesOrder.create')->with('customers',$customers);
    }
}
