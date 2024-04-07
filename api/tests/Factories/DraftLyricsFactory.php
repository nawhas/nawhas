<?php

namespace Tests\Factories;

use App\Modules\Library\Models\DraftLyrics;
use App\Modules\Library\Models\Track;
use App\Modules\Lyrics\Documents\Document;
use \App\Modules\Lyrics\Documents\Factory as DocumentFactory;
use App\Modules\Lyrics\Documents\Format;

class DraftLyricsFactory extends Factory
{
    /**
     * @throws \JsonException
     */
    protected function defaults(): array
    {
        $document = \App\Modules\Lyrics\Documents\JsonV1\Document::fromJson(/** @lang JSON */ <<<JSON
        {
           "meta": {
               "timestamps": true
           },
           "data": [
               {
                   "timestamp": 0,
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 },
                        { "text": "some text goes here", "repeat": 4 }
                   ],
                   "type": "normal"
               },
               {
                   "timestamp": null,
                   "lines": [
                        { "text": "", "repeat": 0 }
                   ],
                   "type": "spacer"
               },
               {
                   "timestamp": 14.04,
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 }
                   ]
               },
               {
                   "timestamp": 19.044425,
                   "lines": [
                        { "text": "some text goes here", "repeat": 0 },
                        { "text": "some text goes here", "repeat": 2 }
                   ]
               }
           ]
        }
        JSON);
        return [
            'document' => $document
        ];
    }

    public function create(?Track $track = null, array $attributes = []): DraftLyrics
    {
        $track = $track ?? $this->getTrackFactory()->create();
        $values = $this->merge($attributes);

        return DraftLyrics::create($track->id, $values->get('document'));
    }

    /**
     * @throws \JsonException
     */
    public function generateDocument(): Document
    {
        return DocumentFactory::create("Sample Lyrics", Format::PlainText);
    }
}
