<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\Supplier;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class SuppliersController extends Controller
{

    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $suppliers = Supplier::paginate(Config::get('common.pagination'));
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        try {

            DB::transaction(function () use ($request) {
                $supplier = New Supplier();
                $supplier->suppliers_type = $request->input('suppliers_type');
                $supplier->company_name = $request->input('company_name');
                $supplier->company_address = $request->input('company_address');
                $supplier->company_office_phone = $request->input('company_office_phone');
                $supplier->company_office_fax = $request->input('company_office_fax');
                $supplier->contact_person = $request->input('contact_person');
                $supplier->contact_person_phone = $request->input('contact_person_phone');
                $supplier->supplier_description = $request->input('supplier_description');
                $supplier->status = $request->input('status');
                $supplier->created_at = time();
                $supplier->created_by = Auth::user()->id;
                $supplier->save();

                //Personal Account Creation
                $personal = new PersonalAccount();
                $personal->person_type = Config::get('common.person_type_supplier');
                if (!empty($request->input('balance'))) {
                    $personal->balance = $request->input('balance');
                }

                if (!empty($request->input('due'))) {
                    $personal->due = $request->input('due');
                }
                $personal->person_id = $supplier->id;
                $personal->created_by = Auth::user()->id;
                $personal->created_at = time();
                $personal->save();

                $year = CommonHelper::get_current_financial_year();
                $user = Auth::user();
                $time = time();

                if (!empty($request->input('balance'))) {
                    // Update Workspace Ledger
                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 41000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                    $workspaceLedger->balance += $request->input('balance');
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_by = $time;
                    $workspaceLedger->save();
                }

                if (!empty($request->input('due'))) {
                    // Update Workspace Ledger
                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 12000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                    $workspaceLedger->balance += $request->input('due');
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_by = $time;
                    $workspaceLedger->save();
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Data cannot Save. Please Try Again');
            return redirect('suppliers');
        }

        Session()->flash('flash_message', 'Data has been Saved');
        return redirect('suppliers');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit')->with('supplier', $supplier);
    }

    public function update($id, SupplierRequest $request)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->suppliers_type = $request->input('suppliers_type');
        $supplier->company_name = $request->input('company_name');
        $supplier->company_address = $request->input('company_address');
        $supplier->company_office_phone = $request->input('company_office_phone');
        $supplier->company_office_fax = $request->input('company_office_fax');
        $supplier->contact_person = $request->input('contact_person');
        $supplier->contact_person_phone = $request->input('contact_person_phone');
        $supplier->supplier_description = $request->input('supplier_description');
        $supplier->status = $request->input('status');
        $supplier->updated_at = time();
        $supplier->updated_by = Auth::user()->id;
        $supplier->update();
        Session()->flash('flash_message', 'Data has been Updated');
        return redirect('suppliers');
    }
}
