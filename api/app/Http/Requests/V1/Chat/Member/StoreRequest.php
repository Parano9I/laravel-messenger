<?php

namespace App\Http\Requests\V1\Chat\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'member_id' => [
                'required', 'exists:users,id',
                Rule::unique('chats_users', 'user_id')->where('chat_id', $this->route('chat')->id)
            ]
        ];
    }
}
