<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Entities\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'email' => [
                'required',
                'string',
                Rule::unique(User::class,'email'),
            ],
            'password' => ['required', 'string'],
            'nickname' => [
                'sometimes',
                'string',
                'nullable',
                Rule::unique(User::class,'nickname'),
            ],
        ];
    }
}
