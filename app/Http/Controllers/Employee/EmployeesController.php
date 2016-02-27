<?php

namespace App\Http\Controllers\Employee;

use App\Http\Requests;
use App\Models\Employee;
use App\Models\Designation;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\Workspace;
use App\Models\WorkspaceLedger;
use App\Models\GeneralLedger;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use DB;
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
        $workspaces= Workspace::lists('name','id');
        return view('employees.create', compact('designations','workspaces'));
    }

    public function store(EmployeeRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $employee = New Employee;
            $employee->name = $request->input('name');
            $employee->mobile = $request->input('mobile');
            $employee->email = $request->input('email');
            $employee->present_address = $request->input('present_address');
            $employee->permanent_address = $request->input('permanent_address');
            $employee->dob = $request->input('dob');
            $employee->designation_id = $request->input('designation_id');
            $employee->workspace_id = $request->input('workspace_id');
            $employee->employee_type = $request->input('employee_type');
            $employee->joining_date = $request->input('joining_date');
            $employee->created_by = Auth::user()->id;
            $employee->created_at = time();
            $employee->save();
            $insertedId = $employee->id;

            $personalAccount = New PersonalAccount;
            $personalAccount->person_type = Config::get('common.person_type_employee');
            $personalAccount->person_id = $insertedId;
            $personalAccount->balance = $request->input('balance');
            $personalAccount->due = $request->input('due');
            $personalAccount->created_by = Auth::user()->id;
            $personalAccount->created_at = time();
            $personalAccount->save();

            // Impacts on accounting tables
            if($request->input('balance')>0)
            {
                $workspace_id = Auth::user()->workspace_id;
                $accountPayableCode = 41000;
                $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'account_code'=>$accountPayableCode,'balance_type'=>Config::get('common.balance_type_intermediate')])->first();
                $accountPayableWorkspaceData->balance += $request->input('balance');
                $accountPayableWorkspaceData->update();

                // General Journal Table Impact
                $generalJournal = New GeneralJournal;
                $generalJournal->date = time();
                $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                $generalJournal->reference_id = $insertedId;
                $generalJournal->year = date('Y');
                $generalJournal->account_code = $accountPayableCode;
                $generalJournal->workspace_id = $workspace_id;
                $generalJournal->amount = $request->input('balance');
                $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $generalJournal->created_by = Auth::user()->id;
                $generalJournal->created_at = time();
                $generalJournal->save();
            }

            if($request->input('due')>0)
            {
                $workspace_id = Auth::user()->workspace_id;
                $accountReceivableCode = 12000;
                $accountReceivableWorkspaceData = WorkspaceLedger::where(['workspace_id'=>$workspace_id, 'account_code'=>$accountReceivableCode,'balance_type'=>Config::get('common.balance_type_intermediate')])->first();
                $accountReceivableWorkspaceData->balance += $request->input('due');
                $accountReceivableWorkspaceData->update();

                $generalJournal = New GeneralJournal;
                $generalJournal->date = time();
                $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                $generalJournal->reference_id = $insertedId;
                $generalJournal->year = date('Y');
                $generalJournal->account_code = $accountReceivableCode;
                $generalJournal->workspace_id = $workspace_id;
                $generalJournal->amount = $request->input('due');
                $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $generalJournal->created_by = Auth::user()->id;
                $generalJournal->created_at = time();
                $generalJournal->save();
            }

            DB::commit();
            Session()->flash('flash_message', 'Employee has been created!');
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            Session()->flash('flash_message', 'Employee not created!');
        }

        return redirect('employees');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $designations = Designation::lists('name', 'id');
        $workspaces= Workspace::lists('name','id');
        return view('employees.edit', compact('employee', 'designations','workspaces'));
    }

    public function update($id, EmployeeRequest $request)
    {
        $employee = Employee::findOrFail($id);
        $employee->name = $request->input('name');
        $employee->mobile = $request->input('mobile');
        $employee->email = $request->input('email');
        $employee->present_address = $request->input('present_address');
        $employee->permanent_address = $request->input('permanent_address');
        $employee->dob = $request->input('dob');
        $employee->designation_id = $request->input('designation_id');
        $employee->workspace_id = $request->input('workspace_id');
        $employee->employee_type = $request->input('employee_type');
        $employee->joining_date = $request->input('joining_date');
        $employee->updated_by = Auth::user()->id;
        $employee->updated_at = time();
        $employee->update();

        Session()->flash('flash_message', 'Employee has been updated!');
        return redirect('employees');
    }
}
