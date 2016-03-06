<?php

namespace App\Http\Requests;

class SalaryGeneratorRequest extends Request
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

        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                return [];
            }
            case 'POST': {
                return [
                    'month' => 'required|numeric',
                    'employee_type' => 'required|numeric',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [];
            }
            default:
                break;
        }
    }
}
