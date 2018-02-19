<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class NewUserRequest extends FormRequest
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
            "name"=>"required|min:5|max:255|string|regex:/[a-zA-Z ]",
            "email"=>"required|email|unique:users,email",
            "passport"=>"required",
            "roles"=>"required|array",
            "roles.*"=>"required|string|exists:roles,name"
            //
        ];
    }
}
