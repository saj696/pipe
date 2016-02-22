<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TransactionRecorderRequest extends Request
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
            'date'=>'required|date',
            'account_code'=>'required|integer',
            'to_whom_type'=>'required_if:account_code, 41000',
            'to_whom'=>'required_if:account_code, 41000',
            'from_whom_type'=>'required_if:account_code, 12000, 20000, 30000',
            'from_whom'=>'required_if:account_code, 12000, 20000, 30000',
            'total_amount'=>'required_without:account_code, 50000, 60000',
            'amount'=>'required|numeric',
        ];
    }
}
