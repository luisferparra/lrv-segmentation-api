<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RequestNewFieldTableControl extends FormRequest
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

		$apiNameUniqueRule = 'unique_slug:aaaa_table_controls,api_name';
		$apiNameUniqueRuleWithoutDash = 'unique_slug_without_middle_dash:aaaa_table_controls,name';

		if (isset($this->tableControl)) {
			$apiNameUniqueRule .= "," . $this->tableControl->id;
			$apiNameUniqueRuleWithoutDash .= "," . $this->tableControl->id;

		}


		return [
			'name' => array('required', 'regex:/[a-zA-Z ]+/', 'min:5', 'max:124', $apiNameUniqueRuleWithoutDash),
			'action' => ['required'],
			'description' => ['required', 'regex:/[a-zA-Z ]+/', 'max:255', 'min:5'],
			'api_name' => array('required', 'regex:/[a-zA-Z ]+/', 'min:5', 'max:255', $apiNameUniqueRule),
			'data_type_id' => ['required', 'exists:data_types,id'],
			'crm_columns_id'=>['nullable','numeric','exists:crm-data.crm_columns,id'],
			
		];
	}
}
