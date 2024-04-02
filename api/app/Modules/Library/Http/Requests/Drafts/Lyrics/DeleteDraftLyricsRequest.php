<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests\Drafts\Lyrics;

use App\Http\Requests\Request;

class DeleteDraftLyricsRequest extends Request
{
    public function rules(): array
    {
        return [
            'draft_lyrics_id' => ['required', 'uuid']
        ];
    }

    public function getDraftLyricsId(): string
    {
        return $this->get('draft_lyrics_id');
    }
}
