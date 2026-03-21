<?php

declare(strict_types=1);

namespace App\Modules\Stories\Http\Requests;

use App\Modules\Stories\Models\Story;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class CreateStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'hero_image_url' => ['nullable', 'string', 'max:2048'],
            'display_date' => ['nullable', 'date_format:Y-m-d'],
            'published' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $title = (string) $this->input('title', '');
            $slugInput = $this->input('slug');
            $effective = ($slugInput !== null && $slugInput !== '')
                ? Str::slug((string) $slugInput)
                : Str::slug($title);
            if ($effective === '') {
                return;
            }
            if (Story::query()->where('slug', $effective)->exists()) {
                $validator->errors()->add('slug', 'A story with this slug already exists.');
            }
        });
    }

    /**
     * @return array{
     *     title: string,
     *     slug?: string|null,
     *     excerpt?: string|null,
     *     body?: string|null,
     *     hero_image_url?: string|null,
     *     display_date?: string|null,
     *     published: bool,
     * }
     */
    public function storyPayload(): array
    {
        $v = $this->validated();

        return [
            'title' => $v['title'],
            'slug' => $v['slug'] ?? null,
            'excerpt' => $v['excerpt'] ?? null,
            'body' => $v['body'] ?? null,
            'hero_image_url' => $v['hero_image_url'] ?? null,
            'display_date' => $v['display_date'] ?? null,
            'published' => array_key_exists('published', $v) ? (bool) $v['published'] : false,
        ];
    }
}
