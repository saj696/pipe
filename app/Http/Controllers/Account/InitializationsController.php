<?php

namespace App\Http\Controllers\Account;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\InitializationRequest;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use App\Models\Workspace;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class InitializationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $workspaces = Workspace::with('workspaceLedger')->where('status', 1)->paginate(Config::get('common.pagination'));
        return view('initializations.index', compact('workspaces'));
    }

    public function edit($id)
    {
        $initializations = WorkspaceLedger::where('workspace_id', $id)->get();
        $accounts = ChartOfAccount::where('status', 1)->select('name', 'code')->get();
        return view('initializations.edit', compact('accounts', 'initializations', 'id'));
    }

    public function update($id, InitializationRequest $request)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $currentYear = CommonHelper::get_current_financial_year();
                $balanceInput = $request->input('balance');
                foreach ($balanceInput as $code => $amount) {
                    $WorkspaceLedger = New WorkspaceLedger;
                    $GeneralLedger = New GeneralLedger;

                    $WorkspaceLedger->workspace_id = $id;
                    $WorkspaceLedger->year = $currentYear;
                    $WorkspaceLedger->account_code = $code;
                    $WorkspaceLedger->balance_type = Config::get('common.balance_type_opening');
                    $WorkspaceLedger->balance = $amount;
                    $WorkspaceLedger->created_by = Auth::user()->id;
                    $WorkspaceLedger->created_at = time();
                    $WorkspaceLedger->save();

                    $WorkspaceLedger = New WorkspaceLedger; // Intermediate row insert

                    $WorkspaceLedger->workspace_id = $id;
                    $WorkspaceLedger->year = $currentYear;
                    $WorkspaceLedger->account_code = $code;
                    $WorkspaceLedger->balance_type = Config::get('common.balance_type_intermediate');
                    $WorkspaceLedger->balance = $amount;
                    $WorkspaceLedger->created_by = Auth::user()->id;
                    $WorkspaceLedger->created_at = time();
                    $WorkspaceLedger->save();

                    $existingGeneralData = GeneralLedger::where(['account_code' => $code, 'balance_type' => Config::get('common.balance_type_opening'), 'year' => $currentYear])->first();

                    if ($existingGeneralData) {
                        $existingGeneral = GeneralLedger::firstOrNew(['account_code' => $code, 'balance_type' => Config::get('common.balance_type_opening'), 'year' => $currentYear]);

                        $existingGeneral->year = $currentYear;
                        $existingGeneral->account_code = $code;
                        $existingGeneral->balance_type = Config::get('common.balance_type_opening');
                        $existingGeneral->balance = $existingGeneralData->balance + $amount;
                        $existingGeneral->updated_by = Auth::user()->id;
                        $existingGeneral->updated_at = time();
                        $existingGeneral->update();
                    } else {
                        $GeneralLedger->year = $currentYear;
                        $GeneralLedger->account_code = $code;
                        $GeneralLedger->balance_type = Config::get('common.balance_type_opening');
                        $GeneralLedger->balance = $amount;
                        $GeneralLedger->created_by = Auth::user()->id;
                        $GeneralLedger->created_at = time();
                        $GeneralLedger->save();
                    }

//                // General Intermediate Data Insert/ Update
//                $GeneralLedger = New GeneralLedger;
//                $existingGeneralIntermediateData = GeneralLedger::where(['account_code' => $code, 'balance_type' => Config::get('common.balance_type_intermediate')])->first();
//
//                if($existingGeneralIntermediateData)
//                {
//                    $existingGeneralIntermediate = GeneralLedger::firstOrNew(['account_code' => $code, 'balance_type' => Config::get('common.balance_type_intermediate')]);
//
//                    $existingGeneralIntermediate->year = date('Y');
//                    $existingGeneralIntermediate->account_code = $code;
//                    $existingGeneralIntermediate->balance_type = Config::get('common.balance_type_intermediate');
//                    $existingGeneralIntermediate->balance = $existingGeneralData->balance + $amount;
//                    $existingGeneralIntermediate->updated_by = Auth::user()->id;
//                    $existingGeneralIntermediate->updated_at = time();
//                    $existingGeneralIntermediate->update();
//                }
//                else
//                {
//                    $GeneralLedger->year = date('Y');
//                    $GeneralLedger->account_code = $code;
//                    $GeneralLedger->balance_type = Config::get('common.balance_type_intermediate');
//                    $GeneralLedger->balance = $amount;
//                    $GeneralLedger->created_by = Auth::user()->id;
//                    $GeneralLedger->created_at = time();
//                    $GeneralLedger->save();
//                }
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Accounts not Initialized!');
            return redirect('initializations');
        }

        Session()->flash('flash_message', 'Accounts Initialized!');
        return redirect('initializations');
    }
}
