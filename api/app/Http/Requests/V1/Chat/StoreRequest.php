<?php

namespace App\Http\Requests\V1\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name'    => 'required|string|max:100|unique:chats',
            'is_open' => 'required|bool',
            'type'    => 'required|in:personal,group',
            'avatar'  => 'nullable|image|mimes:jpg,png,jpeg|max:2048|dimensions:min_width=80,min_height=80'
        ];
    }
}
