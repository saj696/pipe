<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\ProviderRequest;
use App\Models\Provider;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\WorkspaceLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ProvidersController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $providers = DB::table('providers')
            ->select('*')
            ->where(['status' => 1])
            ->paginate(Config::get('common.pagination'));

        return view('providers.index')->with('providers', $providers);
    }

    public function create()
    {
        return view('providers.create');
    }

    public function store(ProviderRequest $request)
    {
        try {

            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $time = time();
                $date=strtotime(date('d-m-Y'));
                $user = Auth::user();
                $year = CommonHelper::get_current_financial_year();
                $file = $request->file('picture');
                $provider = new Provider();

                $destinationPath = base_path() . '/public/image/provider/';
                if ($request->hasFile('picture')) {
                    $name = time() . $file->getClientOriginalName();
                    $file->move($destinationPath, $name);
                    $inputs['picture'] = $name;
                    $provider->picture = $inputs['picture'];
                }

                $provider->name = $inputs['name'];
                $provider->mobile = $inputs['mobile'];
                $provider->address = $inputs['address'];
                $provider->company_name = $inputs['company_name'];
                $provider->company_address = $inputs['company_address'];
                $provider->created_by = $user->id;
                $provider->created_at = $time;
                $provider->save();

                // Personal Account Creation
                $personal = new PersonalAccount();
                $personal->person_type = Config::get('common.person_type_provider');
                if (!empty($inputs['balance'])) {
                    $personal->balance = $inputs['balance'];
                }

                if (!empty($inputs['due'])) {
                    $personal->due = $inputs['due'];
                }
                $personal->person_id = $provider->id;
                $personal->created_by = Auth::user()->id;
                $personal->created_at = $time;
                $personal->save();

                if ($inputs['balance']>0) {
                    // Update Workspace Ledger
//                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 41000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
//                    $workspaceLedger->balance += $inputs['balance'];
//                    $workspaceLedger->updated_by = $user->id;
//                    $workspaceLedger->updated_by = $time;
//                    $workspaceLedger->save();
//
//                    // Insert into General Journal
//                    $generalJournal = new GeneralJournal();
//                    $generalJournal->date = $date;
//                    $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
//                    $generalJournal->reference_id = $personal->id;
//                    $generalJournal->year = $year;
//                    $generalJournal->account_code = 41000;
//                    $generalJournal->workspace_id = $user->workspace_id;
//                    $generalJournal->amount = $inputs['balance'];
//                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
//                    $generalJournal->created_by = $user->id;
//                    $generalJournal->created_at = $time;
//                    $generalJournal->save();
                }

                if ($inputs['due']>0) {
                    // Update Workspace Ledger
//                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 12000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
//                    $workspaceLedger->balance += $inputs['due'];
//                    $workspaceLedger->updated_by = $user->id;
//                    $workspaceLedger->updated_by = $time;
//                    $workspaceLedger->save();
//
//                    // Insert into General Journal
//                    $generalJournal = new GeneralJournal();
//                    $generalJournal->date = $date;
//                    $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
//                    $generalJournal->reference_id = $personal->id;
//                    $generalJournal->year = $year;
//                    $generalJournal->account_code = 12000;
//                    $generalJournal->workspace_id = $user->workspace_id;
//                    $generalJournal->amount = $inputs['due'];
//                    $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
//                    $generalJournal->created_by = $user->id;
//                    $generalJournal->created_at = $time;
//                    $generalJournal->save();
                }
            });
        } catch (\Exception $e) {
            Session::flash('error_message', 'Failed to create Product & Service Provider. Please try again!');
            return Redirect::back();
        }
        Session::flash('flash_message', 'Product & Service Provider created successfully!');
        return redirect('providers');
    }

    public function edit($id = null)
    {
        $provider = Provider::where(['id' => $id])->first();
        return view('providers.edit')->with('provider', $provider);
    }

    public function update(ProviderRequest $request, $id)
    {
        $inputs = $request->input();
        $file = $request->file('picture');
        $destinationPath = base_path() . '/public/image/provider/';

        if ($request->hasFile('picture')) {
            $name = time() . $file->getClientOriginalName();
            $file->move($destinationPath, $name);
            $inputs['picture'] = $name;
        }

        $inputs['updated_by'] = Auth::user()->id;
        $inputs['updated_at'] = time();

        unset($inputs['_method']);
        unset($inputs['_token']);
        Provider::where(['id' => $id])->update($inputs);

        Session::flash('flash_message', 'Product & Service Provider updated successfully');
        return redirect('providers');
    }
}
