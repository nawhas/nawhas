<?php

declare(strict_types=1);

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
            'nickname' => ['sometimes', 'string', 'nullable', 'unique:App\Entities\User,nickname']
        ];
    }
}
