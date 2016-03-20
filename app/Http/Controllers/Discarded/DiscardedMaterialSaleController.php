<?php

namespace App\Http\Controllers\Discarded;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\DiscardedSalesRequest;
use App\Models\Customer;
use App\Models\DiscardedSales;
use App\Models\DiscardedSalesDetail;
use App\Models\GeneralJournal;
use App\Models\Material;
use App\Models\RawStock;
use App\Models\UsageRegister;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class DiscardedMaterialSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $discardedSales = DiscardedSales::paginate(Config::get('common.pagination'));
        return view('discardedSales.index', compact('discardedSales'));
    }

    public function create()
    {
        $customers = Customer::where('status', 1)->lists('name', 'id');
        $materials = Material::where('type', array_flip(Config::get('common.material_type'))['Discarded'])->lists('name','id');
        return view('discardedSales.create', compact('materials', 'customers'));
    }

    public function store(DiscardedSalesRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $workspace_id = Auth::user()->workspace_id;
                $customer_id = $request->customer_id;
                $total_amount = $request->total_amount;
                $paid_amount = $request->paid_amount;
                $due_amount = $total_amount-$paid_amount;

                $items = $request->items;

                if(($total_amount != $paid_amount) && empty($customer_id))
                {
                    Session()->flash('warning_message', 'Amount not fully paid. Customer required!');
                    throw new \Exception('error');
                }
                else
                {
                    $discardedSales = New DiscardedSales;
                    $discardedSales->year = CommonHelper::get_current_financial_year();
                    $discardedSales->date = $request->date;
                    $discardedSales->customer_id = $request->customer_id;
                    $discardedSales->total_amount = $request->total_amount;
                    $discardedSales->paid_amount = $request->paid_amount;
                    $discardedSales->due_amount = $total_amount - $paid_amount;
                    $discardedSales->created_by = Auth::user()->id;
                    $discardedSales->created_at = time();
                    $discardedSales->save();

                    foreach($items as $key=>$item)
                    {
                        $saleDetail = New DiscardedSalesDetail;
                        $saleDetail->discarded_sales_id = $discardedSales->id;
                        $saleDetail->material_id = $item['material_id'];
                        $saleDetail->quantity = $item['sale_quantity'];
                        $saleDetail->amount = $item['amount'];
                        $saleDetail->created_by = Auth::user()->id;
                        $saleDetail->created_at = time();
                        $saleDetail->save();
                    }

                    if($paid_amount>0)
                    {
                        // Workspace Ledger Discarded Sale Account Debit(+)
                        $discardedWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => 33000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                        $discardedWorkspaceData->balance += $paid_amount;
                        $discardedWorkspaceData->update();
                        // General Journals Insert
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = time();
                        $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                        $generalJournal->reference_id = isset($customer_id)?$customer_id:'';
                        $generalJournal->year = CommonHelper::get_current_financial_year();
                        $generalJournal->account_code = 33000;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $paid_amount;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    }

                    if($due_amount>0)
                    {
                        // Workspace Ledger Account Receivable Account Debit(+)
                        $discardedWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => 12000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                        $discardedWorkspaceData->balance += $due_amount;
                        $discardedWorkspaceData->update();
                        // General Journals Insert
                        $generalJournal = New GeneralJournal;
                        $generalJournal->date = time();
                        $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                        $generalJournal->reference_id = isset($customer_id)?$customer_id:'';
                        $generalJournal->year = CommonHelper::get_current_financial_year();
                        $generalJournal->account_code = 12000;
                        $generalJournal->workspace_id = $workspace_id;
                        $generalJournal->amount = $due_amount;
                        $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                        $generalJournal->created_by = Auth::user()->id;
                        $generalJournal->created_at = time();
                        $generalJournal->save();
                    }
                }
            });
        } catch (\Exception $e) {
            dd($e);
            Session()->flash('error_message', 'Discarded Material Sales Not Done!');
            return redirect('discarded_sale');
        }

        Session()->flash('flash_message', 'Discarded Material Sales Successfully Completed!');
        return redirect('discarded_sale');
    }

    public function edit($id)
    {
        $customers = Customer::where('status', 1)->lists('name', 'id');
        $materials = Material::where('type', array_flip(Config::get('common.material_type'))['Discarded'])->lists('name','id');
        $discardedSales = DiscardedSales::with('DiscardedSalesDetail')->findOrFail($id);
        return view('discardedSales.edit', compact('discardedSales', 'customers', 'materials'));
    }

    public function update($id, DiscardedSalesRequest $request)
    {
        $workspace_id = Auth::user()->workspace_id;
        $customer_id = $request->customer_id;
        $total_amount = $request->total_amount;
        $paid_amount = $request->paid_amount;
        $due_amount = $total_amount-$paid_amount;

        $items = $request->items;

        if(($total_amount != $paid_amount) && empty($customer_id))
        {
            Session()->flash('warning_message', 'Amount not fully paid. Customer required!');
            throw new \Exception('error');
        }
        else
        {
            $discardedSales = DiscardedSales::findOrFail($id);
            $discardedSales->year = CommonHelper::get_current_financial_year();
            $discardedSales->date = $request->date;
            $discardedSales->customer_id = $request->customer_id;
            $discardedSales->total_amount = $request->total_amount;
            $discardedSales->paid_amount = $request->paid_amount;
            $discardedSales->due_amount = $total_amount - $paid_amount;
            $discardedSales->created_by = Auth::user()->id;
            $discardedSales->created_at = time();
            $discardedSales->update();

            foreach($items as $key=>$item)
            {
                $existingSalesDetail = DiscardedSalesDetail::where(['discarded_sales_id'=>$id, 'id'=>$key])->first();
                if($existingSalesDetail)
                {
                    $existingSalesDetail->material_id = $item['material_id'];
                    $existingSalesDetail->quantity = $item['sale_quantity'];
                    $existingSalesDetail->amount = $item['amount'];
                    $existingSalesDetail->update();
                }
                else
                {
                    $saleDetail = New DiscardedSalesDetail;
                    $saleDetail->discarded_sales_id = $discardedSales->id;
                    $saleDetail->material_id = $item['material_id'];
                    $saleDetail->quantity = $item['sale_quantity'];
                    $saleDetail->amount = $item['amount'];
                    $saleDetail->created_by = Auth::user()->id;
                    $saleDetail->created_at = time();
                    $saleDetail->save();
                }
            }

            if($paid_amount>0)
            {
                // Workspace Ledger Discarded Sale Account Debit(+)
                $discardedWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => 33000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                $discardedWorkspaceData->balance += $paid_amount;
                $discardedWorkspaceData->update();
                // General Journals Insert
                $generalJournal = New GeneralJournal;
                $generalJournal->date = time();
                $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                $generalJournal->reference_id = isset($customer_id)?$customer_id:'';
                $generalJournal->year = CommonHelper::get_current_financial_year();
                $generalJournal->account_code = 33000;
                $generalJournal->workspace_id = $workspace_id;
                $generalJournal->amount = $paid_amount;
                $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                $generalJournal->created_by = Auth::user()->id;
                $generalJournal->created_at = time();
                $generalJournal->save();
            }

            if($due_amount>0)
            {
                // Workspace Ledger Account Receivable Account Debit(+)
                $discardedWorkspaceData = WorkspaceLedger::where(['workspace_id' => $workspace_id, 'account_code' => 12000, 'balance_type' => Config::get('common.balance_type_intermediate'), 'year' => CommonHelper::get_current_financial_year()])->first();
                $discardedWorkspaceData->balance += $due_amount;
                $discardedWorkspaceData->update();
                // General Journals Insert
                $generalJournal = New GeneralJournal;
                $generalJournal->date = time();
                $generalJournal->transaction_type = Config::get('common.transaction_type.personal');
                $generalJournal->reference_id = isset($customer_id)?$customer_id:'';
                $generalJournal->year = CommonHelper::get_current_financial_year();
                $generalJournal->account_code = 12000;
                $generalJournal->workspace_id = $workspace_id;
                $generalJournal->amount = $due_amount;
                $generalJournal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                $generalJournal->created_by = Auth::user()->id;
                $generalJournal->created_at = time();
                $generalJournal->save();
            }
        }


        try {
            DB::transaction(function () use ($request, $id) {

            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Usage Register not updated!');
            return redirect('discardedSales');
        }

        Session()->flash('flash_message', 'Usage Register has been updated!');
        return redirect('discardedSales');
    }
}
