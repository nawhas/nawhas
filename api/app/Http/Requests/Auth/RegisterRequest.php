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
            'email' => ['required', 'string'],
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'nickname' => ['sometimes', 'string']
        ];
    }

    public function credentials(): array
    {
        return $this->only(['email', 'password']);
    }
}
