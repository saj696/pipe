<?php

namespace App\Http\Controllers\Account;

use App\Http\Requests;
use App\Models\TransactionRecorder;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Supplier;
use Carbon\Carbon;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRecorderRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class TransactionRecordersController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $recorders = TransactionRecorder::paginate(Config::get('common.pagination'));
        $accounts = ChartOfAccount::whereIn('code', Config::get('common.transaction_accounts'))->lists('name', 'code');
        $status = Config::get('common.status');
        return view('transactionRecorders.index', compact('recorders', 'status', 'accounts'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::whereIn('code', Config::get('common.transaction_accounts'))->lists('name', 'code');
        $types = Config::get('common.sales_customer_type');
        $years = CommonHelper::get_years();
        return view('transactionRecorders.create', compact('accounts', 'types', 'years'));
    }

    public function store(TransactionRecorderRequest $request)
    {
        $recorder = New TransactionRecorder;

        $slice = substr($request->account_code, 0,1);

        if($slice==1 || $slice==2 || $slice==3)
        {
            $recorder->from_whom_type = $request->from_whom_type;
            $recorder->from_whom = $request->from_whom;
            $recorder->total_amount = $request->total_amount;
            $recorder->amount = $request->amount;
            $recorder->transaction_detail = $request->transaction_detail;
        }
        elseif($slice==4)
        {
            $recorder->to_whom_type = $request->to_whom_type;
            $recorder->to_whom = $request->to_whom;
            $recorder->total_amount = $request->total_amount;
            $recorder->amount = $request->amount;
            $recorder->transaction_detail = $request->transaction_detail;
        }
        elseif($slice==5 || $slice==6)
        {
            $recorder->amount = $request->amount;
        }

        $recorder->date = $request->date;
        $recorder->year = date('Y', strtotime($request->date));
        $recorder->account_code = $request->account_code;
        $recorder->created_by = Auth::user()->id;
        $recorder->created_at = time();
        $recorder->save();

        Session()->flash('flash_message', 'Transaction Recorder has been created!');
        return redirect('recorders');
    }

    public function edit($id)
    {
        $accounts = ChartOfAccount::whereIn('code', Config::get('common.transaction_accounts'))->lists('name', 'code');
        $types = Config::get('common.sales_customer_type');
        $recorder = TransactionRecorder::findOrFail($id);
        $employees = Employee::where('status', 1)->lists('name', 'id');
        $suppliers = Supplier::where('status', 1)->lists('company_name', 'id');
        $customers = Customer::where('status', 1)->lists('name', 'id');
        $years = CommonHelper::get_years();
        return view('transactionRecorders.edit', compact('recorder','accounts', 'types', 'employees', 'suppliers', 'customers', 'years'));
    }

    public function update($id, TransactionRecorderRequest $request)
    {
        $recorder = TransactionRecorder::findOrFail($id);

        $slice = substr($request->account_code, 0,1);

        if($slice==1 || $slice==2 || $slice==3)
        {
            $recorder->from_whom_type = $request->from_whom_type;
            $recorder->from_whom = $request->from_whom;
            $recorder->total_amount = $request->total_amount;
            $recorder->amount = $request->amount;
            $recorder->transaction_detail = $request->transaction_detail;
        }
        elseif($slice==4)
        {
            $recorder->to_whom_type = $request->to_whom_type;
            $recorder->to_whom = $request->to_whom;
            $recorder->total_amount = $request->total_amount;
            $recorder->amount = $request->amount;
            $recorder->transaction_detail = $request->transaction_detail;
        }
        elseif($slice==5 || $slice==6)
        {
            $recorder->amount = $request->amount;
        }

        $recorder->date = $request->date;
        $recorder->year = date('Y', strtotime($request->date));
        $recorder->account_code = $request->account_code;
        $recorder->updated_by = Auth::user()->id;
        $recorder->updated_at = time();
        $recorder->update();

        Session()->flash('flash_message', 'Transaction Recorder has been updated!');
        return redirect('recorders');
    }
}
