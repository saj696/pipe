<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\FinancialYear;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class FinancialYearSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $financialYears = FinancialYear::paginate(10);
        return view('financialYearSetup.index')->with(compact('financialYears'));
    }

    public function create()
    {

        return view('financialYearSetup.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'year' => 'required|unique:financial_years',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        try {

            DB::transaction(function () use ($request) {

                FinancialYear::where('status', '=', 1)->update(['status'=>0]);

                $financial_year = new FinancialYear();
                $financial_year->year = $request->year;
                $financial_year->start_date = strtotime($request->start_date);
                $financial_year->end_date = strtotime($request->end_date);
                $financial_year->created_by = Auth::user()->id;
                $financial_year->created_at = time();
                $financial_year->save();
            });

        } catch (\Exception $e) {
            Session()->flash('error_message', 'Financial Year can not be created. Please Try again.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Financial Year created Successfully.');
        return redirect('financial_year');
    }
}
