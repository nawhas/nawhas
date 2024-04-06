<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests\Drafts\Lyrics;

use App\Http\Requests\Request;
use App\Modules\Lyrics\Documents\Document;
use App\Modules\Lyrics\Documents\Factory as DocumentFactory;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Validation\Rule;

class UpdateDraftLyricsRequest extends Request
{
    public function rules(): array
    {
        return [
            'document' => [
                'required',
                'array',
                'size:2'
            ],
            'document.content' => ['required'],
            'document.format' => ['required', Rule::enum(Format::class)]
        ];
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
        return $this->get('document.content');
    }

    public function getDocumentFormat(): Format
    {
        return Format::from($this->get('document.format'));
    }
}
