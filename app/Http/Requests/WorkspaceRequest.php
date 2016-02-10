<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class WorkspaceRequest extends Request
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
        return [
            'name'=>'required',
            'type'=>'required|integer',
            'location'=>'required',
            'parent'=>'required|integer',
            'status'=>'required|integer'
        ];
    }
}
