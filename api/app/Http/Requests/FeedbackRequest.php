<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => ['required', 'in:bug,feature,general'],
            'summary' => ['required', 'max:60', 'string'],
            'details' => ['string', 'nullable'],
            'email' => ['string', 'nullable', 'email'],
        ];
    }
}
