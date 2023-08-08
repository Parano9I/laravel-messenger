<?php

namespace App\Http\Requests\V1\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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

        $chat = $this->route('chat');

        return [
            'name'    => [
                'required', 'string', 'max:100',
                Rule::unique('chats')->ignore($chat->id)
            ],
            'is_open' => 'required|bool',
            'type'    => 'required|in:personal,group',
            'avatar'  => 'image|mimes:jpg,png,jpeg|max:2048|dimensions:min_width=80,min_height=80'
        ];
    }
}
