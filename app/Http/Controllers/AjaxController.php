<?php

namespace App\Http\Controllers;


use App\Helpers\CommonHelper;
use App\Http\Requests;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Module;
use App\Models\PersonalAccount;
use App\Models\Provider;
use App\Models\PurchaseDetail;
use App\Models\RawStock;
use App\Models\Salary;
use App\Models\Supplier;
use App\Models\TransactionRecorder;
use App\Models\Wage;
use App\Models\Workspace;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use stdClass;

class AjaxController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function getModules(Request $request)
    {
        $component_id = $request->input('component_id');
        $data = Module::where('component_id', '=', $component_id)->lists('name_en', 'id');
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
        $customers = Customer::where('status', 1)->lists('name', 'id');
        $dropdown = view('ajaxView.customerDropDown')->with('customers', $customers)->render();
        return response()->json($dropdown);
    }

    public function getSuppliers()
    {
        $suppliers = Supplier::where('status', 1)->lists('company_name', 'id');
        $dropdown = view('ajaxView.supplierDropDown')->with('suppliers', $suppliers)->render();
        return response()->json($dropdown);
    }

    public function getEmployees()
    {
        $employees = Employee::where('status', 1)->lists('name', 'id');
        $dropdown = view('ajaxView.employeeDropDown')->with('employees', $employees)->render();
        return response()->json($dropdown);
    }

    public function getProviders()
    {
        $providers = Provider::where('status', 1)->lists('name', 'id');
        $dropdown = view('ajaxView.providerDropDown')->with('providers', $providers)->render();
        return response()->json($dropdown);
    }

    public function getProducts(Request $request)
    {
        $title = $request->input('q');
        $suppliers = DB::table('products')
            ->where('products.status', '=', 1)
            ->where('products.title', 'like', $title . '%')
            ->leftJoin('stocks', function ($join) {
                $join->on('products.id', '=', 'stocks.product_id')
                    ->where('stocks.year', '=', CommonHelper::get_current_financial_year())
                    ->where('stocks.stock_type', '=', Config::get('common.balance_type_intermediate'))
                    ->where('stocks.workspace_id', '=', Auth::user()->workspace_id);
            })->get(['products.id as value', 'products.title as label', 'products.length', 'products.weight', 'products.retail_price', 'products.retail_price', 'products.wholesale_price', 'stocks.quantity']);

        return response()->json($suppliers);
    }

    public function getPersonDueAmount(Request $request)
    {
        $inputs = $request->input();
        $personal = PersonalAccount::where('person_type', $inputs['person_type'])
            ->where('person_id', $inputs['person_id'])
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
        $inputs = $request->input();
        $personal = PersonalAccount::where('person_type', $inputs['person_type'])
            ->where('person_id', $inputs['person_id'])
            ->select('balance')
            ->first();
        if ($personal)
            return response()->json($personal->balance);
        return response()->json(false);
    }

    public function getTransactionRecorderAmount(Request $request)
    {
        $type = $request->input('type');
        $slice = $request->input('slice');
        $person_id = $request->input('person_id');

        $personal = PersonalAccount::where(['person_type' => $type, 'person_id' => $person_id])->first();
        if ($slice == 1) {
            return response()->json(isset($personal->due) ? $personal->due : 0);
        } elseif ($slice == 4) {
            return response()->json(isset($personal->balance) ? $personal->balance : 0);
        }
    }

    //Only Applicable For Generate Payroll
    public function getEmployeeList(Request $request)
    {
        $workspace_id = Auth::user()->workspace_id;
        $inputs = $request->input();
        $salaries = Salary::where(['workspace_id' => $workspace_id, 'month' => $inputs['month'], 'employee_type' => Config::get('common.employee_type.Regular')])->get(['employee_id']);
        $employees = Employee::whereNotIn('id', $salaries)->where(['workspace_id' => $workspace_id, 'status' => 1, 'employee_type' => Config::get('common.employee_type.Regular')])->with('designation')->get();
        $list = view('ajaxView.employeeGenerateSalaryList')->with('employees', $employees)->render();
        return response()->json($list);
    }

    public function getAdjustmentAmounts(Request $request)
    {
        $workspace_id = Auth::user()->workspace_id;
        $account = $request->input('account');
        $year_str = strtotime(date('Y'));

        if ($account == 25000) {
            $purchaseDetail = PurchaseDetail::where(['status' => 1], ['created_at', '>', $year_str])->get(['unit_price', 'quantity']);
            $total_amount = 0;
            $total_quantity = 0;

            foreach ($purchaseDetail as $detail) {
                $total_amount += $detail->quantity * $detail->unit_price;
                $total_quantity += $detail->quantity;
            }

            $unit_price = $total_amount / $total_quantity;
            $stocks = RawStock::where('status', 1)->sum('quantity');
            $remaining_amount = $stocks * $unit_price;
            $return = new stdClass;
            $return->total_amount = $total_amount;
            $return->remaining_amount = $remaining_amount;
            return response()->json($return);
        } elseif ($account == 27000) {
            $supply_amount = TransactionRecorder::where(['workspace_id' => $workspace_id, 'account_code' => $account, 'status' => 1, 'year' => date('Y')])->sum('total_amount');
            return response()->json($supply_amount);
        }
    }

    public function getEmployeePaymentList(Request $request)
    {
        $inputs = $request->input();
        $workspace_id = Auth::user()->workspace_id;
        $salaries = Salary::where(['workspace_id' => $workspace_id, 'month' => $inputs['month'], 'employee_type' => Config::get('common.employee_type.Regular'), 'year' => CommonHelper::get_current_financial_year()])->where('due', '>', 0)->with('employee')->get();
        $list = view('ajaxView.employeeSalaryPaymentList')->with('salaries', $salaries)->render();
        return response()->json($list);

    }

    public function getEmployee(Request $request)
    {
        $title = $request->input('q');
        $employees = DB::table('employees')
            ->where('status', '=', 1)
            ->where('employee_type', '=', Config::get('common.employee_type.Regular'))
            ->where('name', 'like', $title . '%')
            ->get(['id as value', 'name as label']);

        return response()->json($employees);
    }

    public function getEmployeePayment(Request $request)
    {
        $employee_id = $request->employee_id;
        $month = $request->month;
        $salary = Salary::where('employee_id', '=', $employee_id)
            ->where('employee_type', '=', Config::get('common.employee_type.Regular'))
            ->where('year', '=', CommonHelper::get_current_financial_year())
            ->where('month', '=', $month)
            ->where('status', '!=', 4)
            ->first();

        $employee = Employee::where('id', '=', $employee_id)->where('status', '=', 1)->with(['designation', 'workspace'])->first();

        $ajaxView = view('ajaxView.employeeSalaryPayment')->with(compact('salary', 'employee'))->render();
        return response()->json($ajaxView);

    }

    public function getDailyWorkerList(Request $request)
    {
        $current_date = strtotime($request->payment_date);
        $payments = Wage::where('employee_type', '=', Config::get('common.employee_type.Daily Worker'))->where('payment_date', '=', $current_date)->get(['employee_id'])->toArray();
        $emp = array_values(array_column($payments, 'employee_id'));
        $employees = Employee::where('employee_type', '=', Config::get('common.employee_type.Daily Worker'))->whereNotIn('id', $emp)->with('designation')->get();
        $ajaxView = view('payrolls.dailyWagePayment.ajaxView')->with(compact('employees'))->render();

        return response()->json($ajaxView);
    }

    public function getPersonalAccountBalance(Request $request)
    {
        $personal = PersonalAccount::where('person_id', '=', $request->person_id)->where('person_type', '=', $request->person_type)->first();
        if ($personal) {
            return response()->json($personal->balance);
        } else {
            return response()->json(false);
        }

    }
}
