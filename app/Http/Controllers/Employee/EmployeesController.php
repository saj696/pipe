<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\EmployeeRequest;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Workspace;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Request;
use Session;

class EmployeesController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $status = Config::get('common.status');
        $employees = Employee::with('designation')->latest()->paginate(Config::get('common.pagination'));
        return view('employees.index', compact('employees', 'status'));
    }

    public function show($id)
    {
//        $designation = designation::findOrFail($id);
//        return view('designations.show', compact('designation'));
    }

    public function create()
    {
        $user = Auth::user();
        $userGroups = UserGroup::where('level', '>', $user->user_group_id)->lists('name_en', 'id');
        $designations = Designation::lists('name', 'id');
        $workspaces = Workspace::lists('name', 'id');
        return view('employees.create', compact('designations', 'workspaces', 'userGroups'));
    }

    public function store(EmployeeRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $employee = New Employee;

                $file = $request->file('photo');
                $destinationPath = base_path() . '/public/image/employee/';

                if ($request->hasFile('photo')) {
                    $name = time() . $file->getClientOriginalName();
                    $file->move($destinationPath, $name);
                    $employee->photo = $name;
                }

                $employee->name = $request->input('name');
                $employee->mobile = $request->input('mobile');
                $employee->email = $request->input('email');
                $employee->present_address = $request->input('present_address');
                $employee->permanent_address = $request->input('permanent_address');
                $employee->dob = $request->input('dob');
                $employee->designation_id = $request->input('designation_id');
                $employee->workspace_id = $request->input('workspace_id');
                $employee->employee_type = Config::get('common.employee_type.Regular');
                $employee->joining_date = $request->input('joining_date');
                $employee->created_by = Auth::user()->id;
                $employee->created_at = time();
                $employee->save();
                $insertedId = $employee->id;

                // Creation As User
                if ($request->as_user == 1) {
                    $user = New User;
                    $file = $request->file('photo');
                    $destinationPath = base_path() . '/public/image/user/';

                    if ($request->hasFile('photo')) {
                        $name = time() . $file->getClientOriginalName();
                        $file->move($destinationPath, $name);
                        $user->photo = $name;
                    }

                    $user->username = $request->input('username');
                    $user->email = $request->input('email');
                    $user->password = bcrypt($request->input('password'));
                    $user->name_en = $request->input('name');
                    $user->workspace_id = $request->input('workspace_id');
                    $user->user_group_id = $request->input('user_group_id');
                    $user->present_address = $request->input('present_address');
                    $user->permanent_address = $request->input('permanent_address');
                    $user->save();
                }

                // Personal Account Creation
                $personalAccount = New PersonalAccount;
                $personalAccount->person_type = Config::get('common.person_type_employee');
                $personalAccount->person_id = $insertedId;
                $personalAccount->balance = $request->input('balance');
                $personalAccount->due = $request->input('due');
                $personalAccount->created_by = Auth::user()->id;
                $personalAccount->created_at = time();
                $personalAccount->save();

                // Impacts on accounting tables
                if ($request->input('balance') > 0) {
                    $workspace_id = Auth::user()->workspace_id;
                    $accountPayableCode = 41000;
                    $accountPayableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountPayableCode, 'balance_type' => Config::get('common.balance_type_intermediate')])->first();
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

                if ($request->input('due') > 0) {
                    $workspace_id = Auth::user()->workspace_id;
                    $accountReceivableCode = 12000;
                    $accountReceivableWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => $accountReceivableCode, 'balance_type' => Config::get('common.balance_type_intermediate')])->first();
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
            });
        } catch (\Exception $e) {
            dd($e);
            Session()->flash('error_message', 'Employee Not Created!');
            return redirect('employees');
        }

        Session()->flash('flash_message', 'Employee Created Successfully!');
        return redirect('employees');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $designations = Designation::lists('name', 'id');
        $workspaces = Workspace::lists('name', 'id');
        return view('employees.edit', compact('employee', 'designations', 'workspaces'));
    }

    public function update($id, EmployeeRequest $request)
    {
        $employee = Employee::findOrFail($id);
        $file = $request->file('photo');
        $destinationPath = base_path() . '/public/image/employee/';

        if ($request->hasFile('photo')) {
            $name = time() . $file->getClientOriginalName();
            $file->move($destinationPath, $name);
            $employee->photo = $name;
        }

        $employee->name = $request->input('name');
        $employee->mobile = $request->input('mobile');
        $employee->email = $request->input('email');
        $employee->present_address = $request->input('present_address');
        $employee->permanent_address = $request->input('permanent_address');
        $employee->dob = $request->input('dob');
        $employee->designation_id = $request->input('designation_id');
        $employee->workspace_id = $request->input('workspace_id');
        $employee->employee_type = Config::get('common.employee_type.Regular');
        $employee->joining_date = $request->input('joining_date');
        $employee->updated_by = Auth::user()->id;
        $employee->updated_at = time();
        $employee->update();

        Session()->flash('flash_message', 'Employee has been updated!');
        return redirect('employees');
    }
}
