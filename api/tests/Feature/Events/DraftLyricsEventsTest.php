<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\DraftLyrics;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use App\Modules\Lyrics\Documents\Document;
use function App\Support\uuid;

class DraftLyricsEventsTest extends EventsTestCase
{
    private Track $track;
    private Document $document;

    protected function setUp(): void
    {
        parent::setUp();
        $this->track = $this->getTrackFactory()->create();
        $this->document = \App\Modules\Lyrics\Documents\JsonV1\Document::fromJson(/** @lang JSON */ <<<JSON
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
    }

    /**
     * @test
     */
    #[CoversEvent('drafts.lyrics.created')]
    public function it_can_replay_draft_lyrics_created_event(): void
    {
        $id = uuid();
        $track_id = $this->track->id;
        $format = $this->document->getFormat()->value;
        $content = $this->document->getContent();

        $properties = [
            'id' => $id,
            'track_id' => $track_id,
            'document' => [
                'format' => $format,
                'content' => $content
            ]
        ];

        $this->event('drafts.lyrics.created', $properties);
        $this->replay();

        $draftLyrics = DraftLyrics::find($id);
        $this->assertNotNull($draftLyrics);

        $this->assertEquals($track_id, $draftLyrics->track_id);
        $this->assertEquals($format, $draftLyrics->format);
        $this->assertEquals($content, $draftLyrics->content);
    }

    /**
     * @test
     */
    #[CoversEvent('drafts.lyrics.changed')]
    public function it_can_replay_draft_lyrics_changed_event(): void
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create();

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
               }
           ]
        }
        JSON);

        $this->assertNotEquals($draftLyrics->content, $document->getContent());

        $this->event('drafts.lyrics.changed', [
            'id' => $draftLyrics->id,
            'document' => [
                'content' => $document->getContent(),
                'format' => $document->getFormat()->value
            ]
        ]);
        $this->replay();

        $draftLyrics->refresh();
        $this->assertEquals($document->getContent(), $draftLyrics->content);
        $this->assertEquals($document->getFormat()->value, $draftLyrics->format);
    }

    /**
     * @test
     * @throws \JsonException
     */
    #[CoversEvent('drafts.lyrics.published')]
    public function it_can_replay_draft_lyrics_published_event(): void
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create();
        $draftLyricsDocument = \App\Modules\Lyrics\Documents\JsonV1\Document::fromJson($draftLyrics->content);
        $track  = $draftLyrics->track;
        $this->assertNotEquals($track->lyrics, $draftLyricsDocument);

        $this->event('drafts.lyrics.published', [
            'id' => $draftLyrics->id,
            'document' => [
                'content' => $draftLyricsDocument->getContent(),
                'format' => $draftLyricsDocument->getFormat()->value
            ]
        ]);
        $this->replay();
        $track->refresh();

        $this->assertEquals($draftLyricsDocument, $track->lyrics);

        $draftLyrics = DraftLyrics::find($draftLyrics->id);
        $this->assertNull($draftLyrics);
    }

    /**
     * @test
     */
    #[CoversEvent('drafts.lyrics.deleted')]
    public function it_can_replay_draft_lyrics_deleted_event(): void
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create();

        $this->event('drafts.lyrics.deleted', [
            'id' => $draftLyrics->id
        ]);
        $this->replay();

        $draftLyrics = DraftLyrics::find($draftLyrics->id);
        $this->assertNull($draftLyrics);
    }
}
