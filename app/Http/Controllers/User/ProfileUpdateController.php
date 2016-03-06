<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Session;

class ProfileUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $user = Auth::user();
        return view('profileUpdate.index', compact('user'));
    }

    public function update($id, ProfileUpdateRequest $request)
    {
        $user = User::findOrFail($id);
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
        $user->name_en = $request->input('name_en');
        $user->name_bn = $request->input('name_bn');
        $user->present_address = $request->input('present_address');
        $user->permanent_address = $request->input('permanent_address');
        $user->updated_by = Auth::user()->id;
        $user->updated_at = time();
        $user->update();

        Session()->flash('flash_message', 'Profile Updated Successfully!');
        return redirect('profile_update');
    }
}
