<?php

namespace App\Http\Controllers\Wages;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\WageRequest;
use App\Models\PersonalAccount;
use App\Models\Wage;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

;

class WagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {

    }

    public function create()
    {
        return view('wages.create');
    }

    public function store(WageRequest $request)
    {
//        dd($request->input());
        try {

            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                foreach ($inputs['selected'] as $employee_id) {
                    $wage = new Wage();
                    $wage->employee_id = $employee_id;
                    $wage->employee_type = $inputs['employee'][$employee_id]['employee_type'];
                    $wage->workspace_id = $inputs['employee'][$employee_id]['workspace_id'];
                    $wage->year = date('Y');
                    $wage->month = $inputs['month'];
                    $wage->salary = $inputs['employee'][$employee_id]['salary'];
                    $wage->extra_hours = $inputs['employee'][$employee_id]['overtime'];
                    $wage->bonus = $inputs['employee'][$employee_id]['bonus'];
                    $wage->cut = $inputs['employee'][$employee_id]['cut'];
                    $wage->net = $inputs['employee'][$employee_id]['net'];
                    $wage->net = $inputs['employee'][$employee_id]['net'];
                    $wage->created_by = Auth::user()->id;
                    $wage->created_by = time();
                    $wage->save();

                    $personalAccount = PersonalAccount::where(['person_id' => $employee_id, 'person_type' => Config::get('common.person_type_employee')])->first();
                    $personalAccount->balance += $inputs['employee'][$employee_id]['net']; //Add
                    $personalAccount->updated_by = Auth::user()->id;
                    $personalAccount->updated_at = time();
                    $personalAccount->save();


                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Salary cannot generate. Please Try again.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Salary generated successfully.');
        return redirect('wages');


    }
}
