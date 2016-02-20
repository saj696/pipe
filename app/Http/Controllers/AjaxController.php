<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Models\Customer;
use App\Models\Module;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Workspace;
use App\Article;
use App\Tag;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Session;
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
}
