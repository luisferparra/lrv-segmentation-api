<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUsersByIdApiRequest extends FormRequest
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


       /*  $validator->sometimes("data.*.id", 'required|numeric', function ($input) {
            
            return $input->input_data_mode=='id';
        });
        $validator->sometimes("data.*.id", 'required|string', function ($input) {
            
            return $input->input_data_mode!='id';
        }); */
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /**
         * Esto lo realizamos así, porque el contenido del array data.*.val.* puede ser integer o string dependiendo del valor de input_data_mode
         */
        $rules = [
            "data" => "array|required",
            "data.*.id" => "array|required|min:1",
            "data.*.id.*" => "required|numeric",
            "data.*.api_name" => array('required', 'regex:/[a-zA-Z ]+/', 'min:5', 'max:255', 'exists:aaaa_table_controls,api_name'),
            "data.*.input_data_mode" => ["required", Rule::in(['id', 'crm', 'val'])],
            "data.*.val" => "required|array|min:1"
        ];
        foreach ($this['data'] as $key => $value) {
            $inputDataMode = $value['input_data_mode'];
            $ruleTxt = "required|" . (($inputDataMode == 'id') ? "numeric" : "string");
            $additionalRule = [
                "data." . $key . ".val.*" => $ruleTxt
            ];
            $rules = array_merge($rules, $additionalRule);
        }
        return $rules;


       /*  return [
            "data"=>"array|required",
            "data.*.id"=>"array|required|min:1",
            "data.*.id.*"=>"required|numeric",
            "data.*.api_name"=>array('required', 'regex:/[a-zA-Z ]+/', 'min:5', 'max:255', 'exists:aaaa_table_controls,api_name'),
            "data.*.input_data_mode"=>["required",Rule::in(['id','crm','val'])],
            "data.*.val"=>"required|array",
            "data.*.val.*"=>"required|numeric|min:1"
            
          /*   "input_data_mode"=>["required",Rule::in(['id','crm','val'])],
            "data"=>"array|required",
//"data.*.id"=>"required|string|sometimes",
            "data.*.val"=>"array|required",
            "data.*.val.*"=>"required|numeric"
             
        ]; */
    }
}
