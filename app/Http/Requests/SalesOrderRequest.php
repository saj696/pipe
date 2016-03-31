<?php

namespace App\Http\Requests;

class SalesOrderRequest extends Request
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
                    'total' => 'required',
                    'product' => 'required|array',
                    'customer_type' => 'required_with:customer_id',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'total' => 'required',
                    'product' => 'required|array',
                ];
            }
            default:
                break;
        }

    }
}
