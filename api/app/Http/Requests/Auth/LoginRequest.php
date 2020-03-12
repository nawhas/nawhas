<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean']
        ];
    }

    public function credentials(): array
    {
        return $this->only(['email', 'password']);
    }

    public function shouldRemember(): bool
    {
        return $this->filled('remember');
    }
}
