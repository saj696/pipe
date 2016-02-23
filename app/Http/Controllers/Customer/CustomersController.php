<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\PersonalAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CustomersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
        $inputs = $request->input();
        DB::beginTransaction();
        try {
            $file = $request->file('picture');
            $destinationPath = base_path() . '/public/image/customer/';
            if ($request->hasFile('picture')) {
                $file->move($destinationPath, $file->getClientOriginalName());
                $inputs['picture'] = $file->getClientOriginalName();
            }
            $inputs['created_by'] = Auth::user()->id;
            $inputs['created_at'] = time();
            $inputs['status'] = 1;

            unset($inputs['_token']);
            $customer=DB::table('customer')
                ->insertGetId($inputs);

            //Personal Account Creation
            $personal= new PersonalAccount();
            $personal->person_type=Config::get('common.person_type_customer');
            $personal->person_id=$customer;
            $personal->created_by=Auth::user()->id;
            $personal->created_at=time();
            $personal->save();

            DB::commit();
            Session::flash('flash_message', 'Customer created successfully');
            return redirect('customers');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('flash_message', 'Failed to create customer. Please Try again.');
            return Redirect::back();
        }
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
            $file->move($destinationPath, $file->getClientOriginalName());
            $inputs['picture'] = $file->getClientOriginalName();
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
