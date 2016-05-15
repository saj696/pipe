<?php

namespace App\Http\Requests;

class BankRequest extends Request
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
            'name' => 'required',
            'account_no' => 'required',
            'account_type' => 'numeric',
            'opening_balance' => 'numeric',
            'account_code' => 'numeric|digits:5|unique:chart_of_accounts,code',
        ];
    }
}
