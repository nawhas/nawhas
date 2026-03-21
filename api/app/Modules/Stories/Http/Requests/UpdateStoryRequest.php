<?php

declare(strict_types=1);

namespace App\Modules\Stories\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'body' => ['sometimes', 'nullable', 'string'],
            'hero_image_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'display_date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'published' => ['sometimes', 'boolean'],
        ];
    }
}
