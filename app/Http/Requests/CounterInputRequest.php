<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CounterInputRequest extends FormRequest
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


    public function withValidator($validator)
    {
        $validator->sometimes("segmentation.data.*.values.*", 'required|numeric', function ($input) {

            return $input->input_data_mode == 'id';
        });
        $validator->sometimes("segmentation.data.*.values.*", 'required|string', function ($input) {

            return $input->input_data_mode != 'id';
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            "input_data_mode" => ["bail","required", Rule::in(['id', 'crm', 'val'])],
            "bbdd_strict_order"=>["bail","required","integer", Rule::in(0,1)],
            "kid"=>["integer"],
            "token"=>["bail","uuid"],//Añadir el exists exists:data_loads,uuid_token
            "segmentation"=>["required"],
            "segmentation.data"=>["bail","required","array"],
            "segmentation.data.*.api_name" => ["bail","required",'regex:/[a-zA-Z ]+/', 'min:5', 'max:255', 'exists:aaaa_table_controls,api_name'], 
            "segmentation.data.*.values"=>"required|array",
            "limits"=>"required",
            "limits.limit"=>"present|nullable|integer"
        ];
        return $rules;
    }
}
