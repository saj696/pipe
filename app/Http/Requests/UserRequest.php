<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\User;


class UserRequest extends Request
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
        $user = User::find($this->users);

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'name_en'=>'required',
                    'username'=>'required|min:3|unique:users',
                    'email'=>'required|email|unique:users',
                    'password'=>'required|min:6',
                    'user_group_id'=>'required'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name_en'=>'required',
                    'username'=>'required|min:3|unique:users,username,'.$user->id,
                    'email'=>'required|email|unique:users,email,'.$user->id,
                    'password'=>'required|min:6',
                    'user_group_id'=>'required'
                ];
            }
            default:break;
        }
    }
}
