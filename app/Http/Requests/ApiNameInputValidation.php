<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Route;


/**
 * Clase que validará la existencia de la entrada (por api, path) de la api_name.
 * De este modo cuando lleguemos al código sabremos que al menos existe
 */

class ApiNameInputValidation extends FormRequest
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
            'api_name' => array('required', 'regex:/[a-zA-Z ]+/', 'min:5', 'max:255', 'exists:aaaa_table_controls,api_name')
        ];
    }


    protected function validationData()
    {
        return array_merge($this->request->all(), [
            'api_name' => Route::input('api_name'),
        ]);
    }
}
