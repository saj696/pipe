<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\User;


class ProfileUpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = User::find($this->profile_update);

        return [
            'name_en'=>'required',
            'username'=>'required|alpha_dash|min:3|unique:users,username,'.$user->id,
            'email'=>'required|email|unique:users,email,'.$user->id,
            'password'=>'required|min:6',
            'photo' => 'image'
        ];
    }
}
