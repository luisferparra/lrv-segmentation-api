<?php

namespace App\Http\Requests;
use Route;
use Illuminate\Validation\Rule;


use Illuminate\Foundation\Http\FormRequest;

class DataBasePutActivateDeactivateInputRequest extends FormRequest
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
            'id'=>'required|numeric|exists:segmentation.bbdd_lists,id',
            'action'=>["required",Rule::in(['activate', 'deactivate'])],
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
