<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewAdminRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_admin_id' => 'required|exists:mysql.users,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_admin_id.required' => 'Please select an Admin account.',
            'user_admin_id.exists' => 'Please select another Admin account.',
        ];
    }
}
