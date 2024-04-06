<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests\Drafts\Lyrics;

use App\Http\Requests\Request;
use App\Modules\Library\Models\DraftLyrics;
use App\Modules\Library\Models\Track;
use App\Modules\Lyrics\Documents\Document;
use App\Modules\Lyrics\Documents\Factory as DocumentFactory;
use App\Modules\Lyrics\Documents\Format;
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
                Rule::exists(Track::class, 'id'),
                Rule::unique(DraftLyrics::class, 'track_id')
            ],
            'document' => [
                'required',
                'array',
                'size:2'
            ],
            'document.content' => ['required', 'string'],
            'document.format' => ['required', Rule::enum(Format::class)]
        ];
    }

    public function getTrackId(): string
    {
        return $this->get('track_id');
    }

    public function getTrack(): Track
    {
        return Track::retrieve($this->getTrackId());
    }

    /**
     * @throws \JsonException
     */
    public function getDocument(): Document
    {
        return DocumentFactory::create($this->getDocumentContent(), $this->getDocumentFormat());
    }

    public function getDocumentContent(): string
    {
        return $this->input('document')['content'];
    }

    public function getDocumentFormat(): Format
    {
        return Format::from((int)$this->input('document')['format']);
    }
}
