<?php

namespace App\Http\Controllers\Report;

use App\Helpers\CommonHelper;
use App\Models\GeneralJournal;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Provider;
use App\Models\Workspace;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DebtorsCreditorsReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        return view('reports.debtorsCreditors.index');
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'person_type' => 'required',
        ]);

        $person_type = $request->person_type;

        if($person_type==1)
        {
            $persons = DB::table('personal_accounts')
                ->select('personal_accounts.*')
                ->where('due', '>', 0)
                ->get();

            foreach($persons as &$person)
            {
                if($person->person_type==Config::get('common.person_type_employee'))
                {
                    $employee=DB::table('employees')->where('id', $person->person_id)->first();
                    $person->person_name = $employee->name;
                    $person->person_contact = $employee->mobile;
                }
                elseif($person->person_type==Config::get('common.person_type_supplier'))
                {
                    $supplier=DB::table('suppliers')->where('id', $person->person_id)->first();
                    $person->person_name = $supplier->company_name;
                    $person->person_contact = $supplier->company_office_phone;
                }
                elseif($person->person_type==Config::get('common.person_type_customer'))
                {
                    $customer=DB::table('customer')->where('id', $person->person_id)->first();
                    $person->person_name = $customer->name;
                    $person->person_contact = $customer->mobile;
                }
                elseif($person->person_type==Config::get('common.person_type_provider'))
                {
                    $provider=DB::table('providers')->where('id', $person->person_id)->first();
                    $person->person_name = $provider->name;
                    $person->person_contact = $provider->mobile;
                }
            }
        }
        elseif($person_type==2)
        {
            $persons = DB::table('personal_accounts')
                ->select('personal_accounts.*')
                ->where('balance', '>', 0)
                ->get();

            $key=0;
            foreach($persons as &$person)
            {
                if($person->person_type==Config::get('common.person_type_employee'))
                {
                    $employee=DB::table('employees')->where('id', $person->person_id)->first();
                    $person->person_name = $employee->name;
                    $person->person_contact = $employee->mobile;

                    $dueSalary = DB::table('salaries')
                        ->select(DB::raw('SUM(net_due) as sum_net_due'), DB::raw('SUM(over_time_due) as sum_over_time_due'), DB::raw('SUM(bonus_due) as sum_bonus_due'))
                        ->where('employee_id', $person->person_id)
                        ->first();

                    $sumDueSalary = $dueSalary->sum_net_due+$dueSalary->sum_over_time_due+$dueSalary->sum_bonus_due;
                    $person->balance = $person->balance+$person->overtime_balance+$person->bonus_balance-$sumDueSalary;

                    if($person->balance==0)
                    {
                        unset($persons[$key]);
                    }
                }
                elseif($person->person_type==Config::get('common.person_type_supplier'))
                {
                    $supplier=DB::table('suppliers')->where('id', $person->person_id)->first();
                    $person->person_name = $supplier->company_name;
                    $person->person_contact = $supplier->company_office_phone;
                }
                elseif($person->person_type==Config::get('common.person_type_customer'))
                {
                    $customer=DB::table('customer')->where('id', $person->person_id)->first();
                    $person->person_name = $customer->name;
                    $person->person_contact = $customer->mobile;
                }
                elseif($person->person_type==Config::get('common.person_type_provider'))
                {
                    $provider=DB::table('providers')->where('id', $person->person_id)->first();
                    $person->person_name = $provider->name;
                    $person->person_contact = $provider->mobile;
                }

                $key++;
            }
        }

        //dd($persons);
        $persons = array_values($persons);

        $ajaxView = view('reports.debtorsCreditors.view', compact('persons', 'person_type'))->render();
        return response()->json($ajaxView);
    }

}
