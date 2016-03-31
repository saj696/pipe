<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\WorkspaceLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CustomersController extends Controller
{


    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $customers = DB::table('customer')
            ->select('*')
            ->where(['status' => 1])
            ->paginate(Config::get('common.pagination'));

//        dd($customers);
        return view('customer.index')->with('customers', $customers);
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(CustomerRequest $request)
    {
//        $inputs = $request->input();
//        dd($inputs);

        try {

            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $time = time();
                $date=strtotime(date('d-m-Y'));
                $user = Auth::user();
                $year = CommonHelper::get_current_financial_year();
                $file = $request->file('picture');
                $customer = new Customer();

                $destinationPath = base_path() . '/public/image/customer/';
                if ($request->hasFile('picture')) {
                    $name = time() . $file->getClientOriginalName();
                    $file->move($destinationPath, $name);
                    $inputs['picture'] = $name;
                }
                $customer->name = $inputs['name'];
                $customer->mobile = $inputs['mobile'];
                $customer->address = $inputs['address'];
                $customer->type = $inputs['type'];
                $customer->business_name = $inputs['business_name'];
                $customer->business_address = $inputs['business_address'];
                $customer->created_by = $user->id;
                $customer->created_at = $time;
                $customer->save();

                //Personal Account Creation
                $personal = new PersonalAccount();
                $personal->person_type = Config::get('common.person_type_customer');
                if (!empty($inputs['balance'])) {
                    $personal->balance = $inputs['balance'];
                }

                if (!empty($inputs['due'])) {
                    $personal->due = $inputs['due'];
                }
                $personal->person_id = $customer->id;
                $personal->created_by = Auth::user()->id;
                $personal->created_at = $time;
                $personal->save();

                if (!empty($inputs['balance'])) {
                    //Update Workspace Ledger
                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 41000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                    $workspaceLedger->balance += $inputs['balance'];
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_by = $time;
                    $workspaceLedger->save();

                    // Insert into General Journal
                    $generalJournal = new GeneralJournal();
                    $generalJournal->date = $date;
                    $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                    $generalJournal->reference_id = $personal->id;
                    $generalJournal->year = $year;
                    $generalJournal->account_code = 41000;
                    $generalJournal->workspace_id = $user->workspace_id;
                    $generalJournal->amount = $inputs['balance'];
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $generalJournal->created_by = $user->id;
                    $generalJournal->created_at = $time;
                    $generalJournal->save();
                }

                if (!empty($inputs['due'])) {
                    //Update Workspace Ledger
                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 12000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                    $workspaceLedger->balance += $inputs['due'];
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_by = $time;
                    $workspaceLedger->save();

                    // Insert into General Journal
                    $generalJournal = new GeneralJournal();
                    $generalJournal->date = $date;
                    $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                    $generalJournal->reference_id = $personal->id;
                    $generalJournal->year = $year;
                    $generalJournal->account_code = 12000;
                    $generalJournal->workspace_id = $user->workspace_id;
                    $generalJournal->amount = $inputs['due'];
                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $generalJournal->created_by = $user->id;
                    $generalJournal->created_at = $time;
                    $generalJournal->save();
                }
            });
        } catch (\Exception $e) {
            dd($e);
            Session::flash('flash_message', 'Failed to create customer. Please Try again.');
            return Redirect::back();
        }
        Session::flash('flash_message', 'Customer created successfully');
        return redirect('customers');
    }

    public function edit($id = null)
    {
        $customer = Customer::where(['id' => $id])->first();
        return view('customer.edit')->with('customer', $customer);
        //dd($customer);
    }

    public function update(CustomerRequest $request, $id)
    {
        $inputs = $request->input();
        $file = $request->file('picture');
        $destinationPath = base_path() . '/public/image/customer/';

        if ($request->hasFile('picture')) {
            $name = time() . $file->getClientOriginalName();
            $file->move($destinationPath, $name);
            $inputs['picture'] = $name;
        }
        $inputs['updated_by'] = Auth::user()->id;
        $inputs['updated_at'] = time();

//        $customer=Customer::findOrFail($id);
        unset($inputs['_method']);
        unset($inputs['_token']);
        Customer::where(['id' => $id])->update($inputs);
        Session::flash('flash_message', 'Customer updated successfully');
        return redirect('customers');
    }
}
