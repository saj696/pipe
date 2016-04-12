<?php

namespace App\Http\Controllers\Report;

use App\Helpers\CommonHelper;
use App\Models\GeneralJournal;
use App\Models\Workspace;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PersonalAccountsReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        return view('reports.personalAccounts.index');
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'person_type' => 'required',
        ]);

        $person_type = $request->person_type;

        if($person_type==Config::get('common.person_type_employee'))
        {
            $persons = DB::table('personal_accounts')
                ->select('personal_accounts.*', 'employees.name as person_name', 'employees.mobile as phone')
                ->where('person_type', Config::get('common.person_type_employee'))
                ->join('employees', 'employees.id', '=', 'personal_accounts.person_id')
                ->get();

            foreach($persons as &$person)
            {
                $dueSalary = DB::table('salaries')
                    ->select(DB::raw('SUM(net_due) as sum_net_due'), DB::raw('SUM(over_time_due) as sum_over_time_due'), DB::raw('SUM(bonus_due) as sum_bonus_due'))
                    ->where('employee_id', $person->person_id)
                    ->first();

                $sumDueSalary = $dueSalary->sum_net_due+$dueSalary->sum_over_time_due+$dueSalary->sum_bonus_due;
                $person->balance = $person->balance+$person->overtime_balance+$person->bonus_balance-$sumDueSalary;
            }
        }
        elseif($person_type==Config::get('common.person_type_supplier'))
        {
            $persons = DB::table('personal_accounts')
                ->select('personal_accounts.*', 'suppliers.contact_person_phone as phone', 'suppliers.company_name as person_name')
                ->where('person_type', Config::get('common.person_type_supplier'))
                ->join('suppliers', 'suppliers.id', '=', 'personal_accounts.person_id')
                ->get();
        }
        elseif($person_type==Config::get('common.person_type_customer'))
        {
            $persons = DB::table('personal_accounts')
                ->select('personal_accounts.*', 'customer.mobile as phone', 'customer.name as person_name')
                ->where('person_type', Config::get('common.person_type_customer'))
                ->join('customer', 'customer.id', '=', 'personal_accounts.person_id')
                ->get();
        }
        elseif($person_type==Config::get('common.person_type_provider'))
        {
            $persons = DB::table('personal_accounts')
                ->select('personal_accounts.*', 'providers.mobile as phone', 'providers.name as person_name')
                ->where('person_type', Config::get('common.person_type_provider'))
                ->join('providers', 'providers.id', '=', 'personal_accounts.person_id')
                ->get();
        }

        $ajaxView = view('reports.personalAccounts.view', compact('persons', 'person_type'))->render();
        return response()->json($ajaxView);
    }

}
