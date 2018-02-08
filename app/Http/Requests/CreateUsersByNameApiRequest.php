<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;


class CreateUsersByNameApiRequest extends ApiNameInputValidation
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
        $validator->sometimes("data.*.id", 'required|numeric', function ($input) {

            return $input->input_data_mode == 'id';
        });
        $validator->sometimes("data.*.id", 'required|string', function ($input) {

            return $input->input_data_mode != 'id';
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return array_merge(parent::rules(), [
            "input_data_mode" => ["required", Rule::in(['id', 'crm', 'val'])],
            "data" => "array|required",
//"data.*.id"=>"required|string|sometimes",
            "data.*.val" => "array|required",
            "data.*.val.*" => "required|numeric|exists:segmentation.bbdd_subscribers,id"
            //
        ]);
    }
}
