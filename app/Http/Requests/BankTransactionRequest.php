<?php

namespace App\Http\Requests;

class BankTransactionRequest extends Request
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
            'bank_id' => 'required',
            'transaction_type' => 'required|numeric',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric',
        ];
    }
}
