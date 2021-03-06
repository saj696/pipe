<?php

namespace App\Http\Requests;

class ReceivePaymentRequest extends Request
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
            'date' => 'required|date',
            'account_code' => 'required|integer',
            'from_whom_type' => 'required|numeric',
            'from_whom' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'amount' => 'required|numeric',
        ];
    }
}
