<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Models\Customer;
use App\Models\Module;
use App\Models\PersonalAccount;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Workspace;
use App\Models\Material;
use App\Models\RawStock;
use App\Models\TransactionRecorder;
use App\Models\PurchaseDetail;
use App\Article;
use App\Tag;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Session;
use stdClass;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AjaxController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function getModules(Request $request)
    {
        $component_id = $request->input('component_id');
        $data = Module::where('component_id', '=', $component_id)->lists('name_en', 'id');;
        return response()->json($data);
    }

    public function checkParents(Request $request)
    {
        $parent_id = $request->input('parent_id');
        $type_id = $request->input('type_id');
        $parent_type_id = Workspace::where('id', '=', $parent_id)->value('type');
        return response()->json($parent_type_id);
    }

    public function getCustomers()
    {
        $customers=Customer::where('status',1)->lists('name','id');
        $dropdown= view('ajaxView.customerDropDown')->with('customers',$customers)->render();
        return response()->json($dropdown);
    }
    public function getSuppliers()
    {
        $suppliers=Supplier::where('status',1)->lists('company_name','id');
        $dropdown= view('ajaxView.supplierDropDown')->with('suppliers',$suppliers)->render();
        return response()->json($dropdown);
    }

    public function getEmployees()
    {
        $employees=Employee::where('status',1)->lists('name','id');
        $dropdown= view('ajaxView.employeeDropDown')->with('employees',$employees)->render();
        return response()->json($dropdown);
    }

    public function getProducts(Request $request)
    {
        $title=$request->input('q');
        $suppliers=Product::select('id as value','title as label','retail_price','wholesale_price')->where('status',1)->where('title','like', $title.'%')->get();
//        dd($suppliers);
        return response()->json($suppliers);
    }

    public function getPersonDueAmount(Request $request)
    {
        $inputs=$request->input();
        $personal=PersonalAccount::where('person_type',$inputs['person_type'])
            ->where('person_id',$inputs['person_id'])
            ->select('due')
            ->first();

        return response()->json($personal->due);
    }
    /*
     * created by mazba
     * use in [purchase return,]
     */
    public function getPersonBalanceAmount(Request $request)
    {
        $inputs=$request->input();
        $personal=PersonalAccount::where('person_type',$inputs['person_type'])
            ->where('person_id',$inputs['person_id'])
            ->select('balance')
            ->first();
        if($personal)
        return response()->json($personal->balance);
        return response()->json(false);
    }

    public function getTransactionRecorderAmount(Request $request)
    {
        $type = $request->input('type');
        $slice = $request->input('slice');
        $person_id = $request->input('person_id');

        $personal = PersonalAccount::where(['person_type'=> $type, 'id'=>$person_id])->first();
        if($slice==1)
        {
            return response()->json($personal->due);
        }
        elseif($slice==4)
        {
            return response()->json($personal->balance);
        }
    }

    public function getAdjustmentAmounts(Request $request)
    {
        $workspace_id = Auth::user()->workspace_id;
        $account = $request->input('account');
        $year_str = strtotime(date('Y'));

        if($account==25000)
        {
            $purchaseDetail = PurchaseDetail::where(['status'=> 1], ['created_at', '>', $year_str])->get(['unit_price', 'quantity']);
            $total_amount = 0;
            $total_quantity = 0;

            foreach($purchaseDetail as $detail)
            {
                $total_amount += $detail->quantity*$detail->unit_price;
                $total_quantity += $detail->quantity;
            }

            $unit_price = $total_amount/$total_quantity;
            $stocks = RawStock::where('status', 1)->sum('quantity');
            $remaining_amount = $stocks*$unit_price;
            $return = new stdClass;
            $return->total_amount = $total_amount;
            $return->remaining_amount = $remaining_amount;
            return response()->json($return);
        }
        elseif($account==27000)
        {
            $supply_amount =  TransactionRecorder::where(['workspace_id'=>$workspace_id, 'account_code'=>$account, 'status'=>1, 'year'=>date('Y')])->sum('total_amount');
            return response()->json($supply_amount);
        }
    }
}
