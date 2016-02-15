<?php

namespace App\Http\Controllers\Employee;

use App\Http\Requests;
use App\Models\Employee;
use App\Models\Designation;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class EmployeesController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $status = Config::get('common.status');
        $employees = Employee::with('designation')->paginate(Config::get('common.pagination'));
        return view('employees.index', compact('employees', 'status'));
    }

    public function show($id)
    {
//        $designation = designation::findOrFail($id);
//        return view('designations.show', compact('designation'));
    }

    public function create()
    {
        $designations = Designation::lists('name', 'id');
        return view('employees.create', compact('designations'));
    }

    public function store(EmployeeRequest $request)
    {
        $employee = New Employee;
        $employee->name = $request->input('name');
        $employee->mobile = $request->input('mobile');
        $employee->email = $request->input('email');
        $employee->present_address = $request->input('present_address');
        $employee->permanent_address = $request->input('permanent_address');
        $employee->dob = $request->input('dob');
        $employee->designation_id = $request->input('designation_id');
        $employee->joining_date = $request->input('joining_date');
        $employee->created_by = Auth::user()->id;
        $employee->created_at = time();

        $employee->save();

        Session()->flash('flash_message', 'Employee has been created!');
        return redirect('employees');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    public function update($id, DesignationRequest $request)
    {
        $designation = Designation::findOrFail($id);
        $designation->name = $request->input('name');
        $designation->salary = $request->input('salary');
        $designation->hourly_rate = $request->input('hourly_rate');
        $designation->updated_by = Auth::user()->id;
        $designation->updated_at = time();
        $designation->update();

        Session()->flash('flash_message', 'Designation has been updated!');
        return redirect('designations');
    }
}
