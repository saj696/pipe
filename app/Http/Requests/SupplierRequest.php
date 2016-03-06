<?php

namespace App\Http\Requests;


class SupplierRequest extends Request
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
            'suppliers_type' => 'required',
            'company_name' => 'required',
            'company_office_phone' => 'required|min:8',
            'company_office_fax' => 'min:8',
            'contact_person_phone' => 'min:8'
        ];
    }

    public function messages()
    {
        return [
            'company_office_phone.min' => 'The company office phone must be at least 8 digits.',
            'company_office_fax.min' => 'The company office fax must be at least 8 digits.',
            'contact_person_phone.min' => 'The contact person phone must be at least 8 digits.',
        ];
    }
}
