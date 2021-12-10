<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:bug,feature,general'],
            'summary' => ['required', 'max:60', 'string'],
            'details' => ['string', 'nullable'],
            'email' => ['string', 'nullable', 'email'],
        ];
    }
}
