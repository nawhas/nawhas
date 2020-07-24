<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:App\Entities\User,email'],
            'password' => ['required', 'string'],
            'nickname' => ['sometimes', 'string', 'nullable', 'unique:App\Entities\User,nickname']
        ];
    }
}
