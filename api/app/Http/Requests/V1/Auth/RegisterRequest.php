<?php

namespace App\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'avatar'   => 'nullable|image|mimes:jpg,png,jpeg|max:2048|dimensions:min_width=80,min_height=80'
        ];
    }
}
