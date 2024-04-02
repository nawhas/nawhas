<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests\Drafts\Lyrics;

use App\Http\Requests\Request;
use App\Modules\Library\Models\Track;
use Illuminate\Validation\Rule;

class CreateDraftLyricsRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'track_id' => [
                'required',
                Rule::exists(Track::class, 'id')
            ],
            'document' => [
                'required',
                'array',
                'size:2',
                'required_with:document.content,document.format'
            ],
            'document.content' => ['required_with:document'],
            'document.format' => ['required_with:document', 'integer']
        ];
    }

    public function track(): Track
    {
        return Track::retrieve($this->get('track_id'));
    }

    public function documentContent(): string
    {
        return $this->get('document.content');
    }

    public function documentFormat(): int
    {
        return $this->get('document.format');
    }
}
