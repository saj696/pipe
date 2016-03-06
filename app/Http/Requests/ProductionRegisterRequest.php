<?php

namespace App\Http\Requests;

class ProductionRegisterRequest extends Request
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
                    'product_id' => 'required|array',
                    'date' => 'required|date',
                    'production' => 'required|array'
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'date' => 'required|date',
                    'production' => 'required|integer'
                ];
            }
            default:
                break;
        }
    }
}
