<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendResetPasswordLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function credentials(): array
    {
        return $this->only(['email']);
    }
}
