<?php

namespace App\Http\Requests;

use App\Models\ChartOfAccount;

class ChartOfAccountRequest extends Request
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
        $account = ChartOfAccount::find($this->charts); // charts = route

        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                return [];
            }
            case 'POST': {
                return [
                    'name' => 'required',
                    'code' => 'required|unique:chart_of_accounts|digits:5'
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'name' => 'required',
                    'code' => 'required|digits:5|unique:chart_of_accounts,code,' . $account->id
                ];
            }
            default:
                break;
        }
    }
}
