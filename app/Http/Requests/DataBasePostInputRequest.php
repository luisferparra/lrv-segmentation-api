<?php

namespace App\Http\Requests;
use Route;

use Illuminate\Foundation\Http\FormRequest;

class DataBasePostInputRequest extends FormRequest
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
        $rule = '';
        if (isset($this->idbbdd))
            $rule = ','.$this->idbbdd;
        
        return [
            'id'=>'required|numeric|unique:segmentation.bbdd_lists,id'.$rule,
            'bbdd'=>'bail|required|regex:/[a-zA-Z]+/|min:2|max:5|unique:segmentation.bbdd_lists,val'.$rule,
            'active'=>'bail|required|numeric|regex:/[01]/'
            //
        ];
    }

    protected function validationData()
    {
        return array_merge($this->request->all(), [
            'id' => Route::input('idbbdd'),
        ]);
    }
}
