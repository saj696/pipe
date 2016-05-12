<?php

namespace App\Http\Requests;

class LoanProviderRequest extends Request
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
            'mobile' => 'required',
            'balance' => 'numeric',
            'due' => 'numeric',
            'picture' => 'image'
        ];
    }

    //Custom Error Message

    public function messages()
    {
        return [
            'picture.image' => 'Please upload an image'
        ];
    }
}
