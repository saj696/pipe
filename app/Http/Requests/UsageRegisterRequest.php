<?php

namespace App\Http\Requests;

class UsageRegisterRequest extends Request
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
                    'date' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'material_id' => 'required|integer',
                    'date' => 'required|date',
                    'usage' => 'required|integer'
                ];
            }
            default:
                break;
        }
    }
}
