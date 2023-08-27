<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;

use function App\Support\uuid;

class TrackEventsTest extends EventsTestCase
{
    private Reciter $reciter;
    private Album $album;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reciter = $this->getReciterFactory()->create();
        $this->album = $this->getAlbumFactory()->create($this->reciter);
    }

    /**
     * @test
     */
    #[CoversEvent('track.created')]
    public function it_can_replay_track_created_event(): void
    {
        $id = uuid();
        $properties = [
            'id' => $id,
            'albumId' => $this->album->id,
            'attributes' => [
                'title' => $this->faker->name,
            ]
        ];
        $this->event('track.created', $properties);

        $this->replay();

        $track = Track::find($id);
        $this->assertNotNull($track);
        $this->assertEquals($this->reciter->id, $track->reciter_id);
        $this->assertEquals($this->album->id, $track->album_id);
        $this->assertEquals($properties['attributes']['title'], $track->title);
    }

    /**
     * @test
     */
    #[CoversEvent('track.changed.title')]
    public function it_can_replay_track_title_changed_event(): void
    {
        $track = $this->getTrackFactory()->create($this->album);

        $title = $this->faker->name;

        $this->event('track.changed.title', [
            'id' => $track->id,
            'title' => $title,
        ]);
        $this->replay();

        $track->refresh();
        $this->assertEquals($title, $track->title);
    }

    /**
     * @test
     */
    #[CoversEvent('track.changed.audio')]
    public function it_can_replay_track_audio_changed_event(): void
    {
        $track = $this->getTrackFactory()->create($this->album);

        $audio = $this->faker->url . '.mp3';

        $this->event('track.changed.audio', [
            'id' => $track->id,
            'path' => $audio,
        ]);
        $this->replay();

        $track->refresh();
        $this->assertEquals($audio, $track->audio);

        // With Null
        $this->event('track.changed.audio', [
            'id' => $track->id,
            'path' => null,
        ]);
        $this->replay();

        $track->refresh();
        $this->assertNull($track->audio);
    }

    /**
     * @test
     */
    #[CoversEvent('track.changed.video')]
    public function it_can_replay_track_video_changed_event(): void
    {
        $track = $this->getTrackFactory()->create($this->album);

        $video = $this->faker->url;

        $this->event('track.changed.video', [
            'id' => $track->id,
            'url' => $video,
            'provider' => 'youtube'
        ]);
        $this->replay();

        $track->refresh();
        $this->assertEquals($video, $track->video);

        // With Null
        $this->event('track.changed.video', [
            'id' => $track->id,
            'url' => null,
            'provider' => 'youtube',
        ]);
        $this->replay();

        $track->refresh();
        $this->assertNull($track->video);
    }

    /**
     * @test
     */
    #[CoversEvent('track.changed.lyrics')]
    public function it_can_replay_track_lyrics_changed_event(): void
    {
        $track = $this->getTrackFactory()->create($this->album);

        // V1 Document
        $document = [
            'content' => implode("\n\n", $this->faker->sentences(4)),
            'format' => 1
        ];

        $this->event('track.changed.lyrics', [
            'id' => $track->id,
            'document' => $document,
        ]);
        $this->replay();

        $track->refresh();
        $this->assertNotNull($track->lyrics);
        $this->assertEquals($document['format'], $track->lyrics->getFormat()->value);
        $this->assertEquals($document['content'], $track->lyrics->getContent());

        // V2 Document
        $document = [
            'content' => json_encode([
                'meta' => ['timestamps' => false],
                'data' => [
                    ['timestamp' => 0, 'type' => 'normal', 'lines' => [
                        ['text' => $this->faker->sentence, 'repeat' => 0],
                    ]],
                ],
            ]),
            'format' => 2
        ];

        $this->event('track.changed.lyrics', [
            'id' => $track->id,
            'document' => $document,
        ]);
        $this->replay();

        $track->refresh();
        $this->assertNotNull($track->lyrics);
        $this->assertEquals($document['format'], $track->lyrics->getFormat()->value);
        $this->assertEquals($document['content'], $track->lyrics->getContent());

        // With Null
        $this->event('track.changed.lyrics', [
            'id' => $track->id,
            'document' => null,
        ]);
        $this->replay();

        $track->refresh();
        $this->assertNull($track->lyrics);
    }

    /**
     * @test
     */
    #[CoversEvent('track.deleted')]
    public function it_can_replay_track_deleted_event(): void
    {
        $track = $this->getTrackFactory()->create($this->album);
        $this->event('track.deleted', [
            'id' => $track->id,
        ]);
        $this->replay();

        $this->assertNull(Track::find($track->id));
    }

    /**
     * @test
     */
    #[CoversEvent('track.viewed')]
    public function it_can_replay_track_viewed_event(): void
    {
        $track = $this->getTrackFactory()->create();
        $this->assertEquals(0, $track->visitsForever());

        $this->event('track.viewed', [
            'id' => $track->id,
            'visited_at' => now(),
        ]);

        $this->replay();
        $track->refresh();

        $this->assertEquals(1, $track->visitsForever());
    }
}
