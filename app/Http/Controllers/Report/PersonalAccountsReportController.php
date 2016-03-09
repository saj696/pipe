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

        if($person_type==1)
        {
            $persons = DB::table('personal_accounts')
                ->select('personal_accounts.*', 'employees.name as person_name', 'employees.mobile as phone')
                ->where('person_type', 1)
                ->join('employees', 'employees.id', '=', 'personal_accounts.person_id')
                ->get();
        }
        elseif($person_type==2)
        {
            $persons = DB::table('personal_accounts')
                ->select('personal_accounts.*', 'suppliers.contact_person_phone as phone', 'suppliers.company_name as person_name')
                ->where('person_type', 2)
                ->join('suppliers', 'suppliers.id', '=', 'personal_accounts.person_id')
                ->get();
        }
        elseif($person_type==3)
        {
            $persons = DB::table('personal_accounts')
                ->select('personal_accounts.*', 'customer.mobile as phone', 'customer.name as person_name')
                ->where('person_type', 3)
                ->join('customer', 'customer.id', '=', 'personal_accounts.person_id')
                ->get();
        }

        $ajaxView = view('reports.personalAccounts.view', compact('persons', 'person_type'))->render();
        return response()->json($ajaxView);
    }

}
