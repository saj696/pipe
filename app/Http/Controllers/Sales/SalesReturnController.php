<?php

namespace App\Http\Controllers\Sales;

use App\Models\Customer;
use App\Models\PersonalAccount;
use App\Models\Stock;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class SalesReturnController extends Controller
{
    public function index()
    {
        $salesReturns = DB::table('sales_return')->select('sales_return.*', 'sales.*')->join('sales_return_details as sales', 'sales_return.id', '=', 'sales.sales_return_id')->get();
        return view('sales.salesReturn.index')->with(compact('salesReturns'));

    }


    public function create()
    {

        $customers = Customer::where('status', 1)->lists('name', 'id');
        return view('sales.salesReturn.create')->with('customers', $customers);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'product' => 'required|array',
            'total' => 'required|numeric',
            'return_type' => 'required',
        ]);

        $inputs = $request->input();
        DB::beginTransaction();

        try {
            $user_id = Auth::user()->id;
            $time = time();
            $data['customer_id'] = $inputs['customer_id'];
            $data['customer_type'] = $inputs['customer_type'];
            $data['total_amount'] = $inputs['total'];
            if ($inputs['return_type'] == 2 || $inputs['return_type'] == 4) {
                $data['due_paid'] = $inputs['due_paid'];
                $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                $personal->due -= $inputs['due_paid'];
                if ($inputs['total'] > $inputs['due_paid']) {
                    $personal->blance += ($inputs['total'] - $inputs['due_paid']);
                }
                $personal->updated_by = $user_id;
                $personal->updated_at = $time;
                $personal->save();
            } elseif ($inputs['return_type'] == 3) {
                $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                $personal->blance += $inputs['total'];
                $personal->updated_by = $user_id;
                $personal->updated_at = $time;
                $personal->save();
            }
            $data['return_type'] = $inputs['return_type'];
            $data['date'] = $time;
            $data['created_by'] = $user_id;
            $data['created_at'] = $time;
            $sales_return_id = DB::table('sales_return')->insertGetId($data);
            unset($data);

            $data['sales_return_id'] = $sales_return_id;
            $data['created_by'] = $user_id;
            $data['created_at'] = $time;
            foreach ($inputs['product'] as $product) {
                $data['product_id'] = $product['product_id'];
                $data['quantity'] = $product['quantity_returned'];
                $data['unit_price'] = $product['unit_price'];
                DB::table('sales_return_details')->insert($data);

                $stock = Stock::find($product['product_id']);
                $stock->quantity += $product['quantity_returned'];
                $stock->updated_by = $user_id;
                $stock->updated_at = $time;
                $stock->save();
            }

            DB::commit();

            Session()->flash('flash_message', 'Sales Returned Successful.');
            return redirect('sales_return');

        } catch (\Exception $e) {
            DB::rollBack();
            Session()->flash('flash_message', 'Sales Returned Failed.');
            return Redirect::back();
        }
    }
}
