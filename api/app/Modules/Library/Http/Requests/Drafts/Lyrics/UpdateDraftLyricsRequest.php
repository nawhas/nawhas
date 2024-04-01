<?php

namespace App\Modules\Library\Http\Requests\Drafts\Lyrics;

use App\Http\Requests\Request;

class UpdateDraftLyricsRequest extends Request
{
    public function rules(): array
    {
        return [
            'draft_lyrics_id' => ['required', 'uuid'],
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

    public function documentContent(): string
    {
        return $this->get('document.content');
    }

    public function documentFormat(): int
    {
        return $this->get('document.format');
    }
}
