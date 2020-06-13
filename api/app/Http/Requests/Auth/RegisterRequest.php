<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'unique:App\Entities\User,email'],
            'password' => ['required', 'string'],
            'nickname' => ['sometimes', 'string', 'unique:App\Entities\User,nickname']
        ];
    }

    public function credentials(): array
    {
        return $this->only(['email', 'password']);
    }
}
