<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Route;
use Illuminate\Validation\Rule;

class UsersByDBInputRequest extends FormRequest
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
        $validator->sometimes("data.*", 'bail|required|numeric|min:1|exists:segmentation.bbdd_users,id', function ($input) {

            return $input->action == 'unsub';
        });
        $validator->sometimes("data.*", 'required|numeric|min:1', function ($input) {

            return $input->action == 'register';
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'=>'required|numeric|exists:segmentation.bbdd_lists,id',
            'action'=>["required",Rule::in(['register', 'unsub'])],
            "data" => "array|required|between:1,1000",
            "data.*" => "required|numeric|min:1",//|exists:segmentation.bbdd_users,id 
             

        ];
    }


    protected function validationData()
    {
        return array_merge($this->request->all(), [
            'id' => Route::input('idbbdd'),
            'action' => Route::input('action'),
        ]);
    }






}
