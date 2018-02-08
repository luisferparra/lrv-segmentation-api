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
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'val_crm' => array('required', 'regex:/[a-zA-Z ]+/', 'min:5', 'max:255', 'unique'),
			'val_normalized' => array('required', 'regex:/[a-zA-Z ]+/', 'min:5', 'max:255', 'unique'),

		];
    }
}
