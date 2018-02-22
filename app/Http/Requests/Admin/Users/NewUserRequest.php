<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class NewUserRequest extends FormRequest
{
    private $req = null;
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
        //$input = $this->request;
        $validator->sometimes("password", 'required|string|min:5|max:20"', function ($input) {

            return (!empty($input->editing) && $input->password != '');
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->req = $this->request->all();
       
        $email = '';
        $pwd = ['password' => "required|string|min:5|max:20"];
        if (!empty($this->req['editing'])) {
            $email = ',' . $this->req['id'];
            if (empty($input->password))
                $pwd = [];
        }

        $rulesValidate = [
            "name" => "required|min:5|max:255|string|regex:/[a-zA-Z ]/",
            "email" => "required|email|unique:users,email" . $email,

            "roles" => "required|array",
            "roles.*" => "required|string|exists:roles,name"
            //
        ];
        $rulesValidate = array_merge($rulesValidate, $pwd);
        return $rulesValidate;
    }
}
