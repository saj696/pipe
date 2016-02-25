<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AdjustmentRequest extends Request
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
//            'account_from'=>'required|unique:adjustments,account_from,year,'.date('Y'),
            'account_from'=>'required|unique:adjustments,account_from,year:'.date('Y'),
            'amount'=>'required'
        ];
    }
}
