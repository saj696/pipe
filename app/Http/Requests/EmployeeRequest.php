<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Auth\User;

class EmployeeRequest extends Request
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
        $employee = Employee::find($this->employees);
        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                return [];
            }
            case 'POST': {
                return [
                    'name' => 'required',
                    'mobile' => 'required|min:11',
                    'joining_date' => 'required|date',
                    'designation_id' => 'required|integer',
                    'workspace_id' => 'required|integer',
//                    'employee_type' => 'required|integer',
                    'photo' => 'image',
                    'username' => 'required_if:as_user,1|alpha_dash|min:3|unique:users',
                    'email' => 'required|email|unique:users',
                    'password' => 'required_if:as_user,1|min:6',
                    'user_group_id' => 'required_if:as_user,1|integer',
                ];
            }
            case 'PUT':
            case 'PATCH': {
            return [
                'name' => 'required',
                'mobile' => 'required|min:11',
                'joining_date' => 'required|date',
                'designation_id' => 'required|integer',
                'workspace_id' => 'required|integer',
//                'employee_type' => 'required|integer',
                'photo' => 'image',
                'email' => 'required|email|unique:employees,email,' . $employee->id,
            ];
            }
            default:
                break;
        }
    }

    public function messages()
    {
        return [
            'username.required_if' => 'Username field is required when you are creating Employee as User',
            'password.required_if' => 'Password field is required when you are creating Employee as User',
            'user_group_id.required_if' => 'User Group field is required when you are creating Employee as User',
            'photo.image' => 'Please upload an image'
        ];
    }
}
