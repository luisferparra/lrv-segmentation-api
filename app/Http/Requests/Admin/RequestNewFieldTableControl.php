<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RequestNewFieldTableControl extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => array('required', 'regex:/[a-zA-Z ]+/', 'min:5', 'max:124'),
			'action' => ['required'],
			'description' => ['required', 'regex:/[a-zA-Z ]+/', 'max:255', 'min:5'],
			'api_name' => array('regex:/[a-zA-Z ]+/', 'min:5', 'max:255'),
			'data_type_id' => ['required', 'exists:data_types,id'],
		];
	}
}
