<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\LoanProviderRequest;
use App\Models\LoanProvider;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\WorkspaceLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LoanProvidersController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $providers = DB::table('loan_providers')
            ->select('*')
            ->where(['status' => 1])
            ->paginate(Config::get('common.pagination'));

        return view('loanProviders.index')->with('providers', $providers);
    }

    public function create()
    {
        return view('loanProviders.create');
    }

    public function store(LoanProviderRequest $request)
    {
        try {

            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $time = time();
                $user = Auth::user();
                $year = CommonHelper::get_current_financial_year();
                $file = $request->file('picture');
                $provider = new LoanProvider();

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
                $personal->person_type = Config::get('common.person_type_loan_provider');
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
                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 41000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                    $workspaceLedger->balance += $inputs['balance'];
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_by = $time;
                    $workspaceLedger->save();
                }

                if ($inputs['due']>0) {
                    // Update Workspace Ledger
                    $workspaceLedger = WorkspaceLedger::where(['workspace_id' => $user->workspace_id, 'account_code' => 12000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])->first();
                    $workspaceLedger->balance += $inputs['due'];
                    $workspaceLedger->updated_by = $user->id;
                    $workspaceLedger->updated_by = $time;
                    $workspaceLedger->save();
                }
            });
        } catch (\Exception $e) {
            Session::flash('error_message', 'Failed to create Loan Provider. Please try again!');
            return Redirect::back();
        }
        Session::flash('flash_message', 'Loan Provider created successfully!');
        return redirect('loan_providers');
    }

    public function edit($id = null)
    {
        $provider = LoanProvider::where(['id' => $id])->first();
        return view('loanProviders.edit')->with('provider', $provider);
    }

    public function update(LoanProviderRequest $request, $id)
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
        LoanProvider::where(['id' => $id])->update($inputs);

        Session::flash('flash_message', 'Loan Provider updated successfully');
        return redirect('loan_providers');
    }
}
