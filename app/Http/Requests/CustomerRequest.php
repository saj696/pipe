<?php

namespace App\Http\Requests;

class CustomerRequest extends Request
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
            'type' => 'required',
            'balance' => 'numeric',
            'due' => 'numeric',
            'business_name' => 'required_if:type,1',
            'business_address' => 'required_if:type,1',
            'picture' => 'image'
        ];
    }

    //Custom Error Message

    public function messages()
    {
        return [
            'business_name.required_if' => 'The business name field is required',
            'business_address.required_if' => 'The business address field is required',
            'picture.image' => 'Please upload an image'
        ];
    }
}
