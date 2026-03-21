<?php

declare(strict_types=1);

namespace App\Modules\Stories\Http\Requests;

use App\Modules\Stories\Models\Story;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('slug') && $this->input('slug') !== null && $this->input('slug') !== '') {
            $this->merge([
                'slug' => Str::slug((string) $this->input('slug')),
            ]);
        }
    }

    public function rules(): array
    {
        /** @var Story $story */
        $story = $this->route('story');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique(Story::class, 'slug')->ignore($story),
            ],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'body' => ['sometimes', 'nullable', 'string'],
            'hero_image_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'display_date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'published' => ['sometimes', 'boolean'],
        ];
    }
}
