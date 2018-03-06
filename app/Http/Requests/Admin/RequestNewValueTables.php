<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RequestNewValueTables extends FormRequest
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
			'val_crm' => array('required', 'regex:/[a-zA-Z 0-9]+/', 'min:1', 'max:255'),
			'val_normalized' => array('required', 'regex:/[a-zA-Z 0-9]+/', 'min:1', 'max:255'),

		];
    }
}
