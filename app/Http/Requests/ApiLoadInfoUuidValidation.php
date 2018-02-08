<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class ApiLoadInfoUuidValidation extends FormRequest
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

    protected function validationData()
    {
        return array_merge($this->request->all(), [
            'uuid_token' => Route::input('uuid_token'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //bail: si falla alguna no sigue mirando validaciones
        return [
            //
            'uuid_token'=>'bail|required|string|uuid|exists:data_loads,uuid_token'
        ];

    }
}
