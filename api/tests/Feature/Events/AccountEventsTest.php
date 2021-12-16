<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Accounts\Events\Saves\TrackSaved;
use App\Modules\Authentication\Models\User;
use App\Modules\Library\Models\Track;

class AccountEventsTest extends EventsTest
{
    private User $user;
    private Track $track;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->getUserFactory()->contributor();
        $this->track = $this->getTrackFactory()->create();
    }

    /**
     * @test
     */
    #[CoversEvent('accounts.saves.track.added')]
    public function it_can_replay_track_saved_event(): void
    {
        $this->assertFalse($this->user->hasSavedTrack($this->track->id));

        $this->event('accounts.saves.track.added', [
            'userId' => $this->user->id,
            'trackId' => $this->track->id,
        ]);
        $this->replay();

        $this->assertTrue($this->user->hasSavedTrack($this->track->id));
    }

    /**
     * @test
     */
    #[CoversEvent('accounts.saves.track.removed')]
    public function it_can_replay_saved_track_removed_event(): void
    {
        event((new TrackSaved($this->track->id))->setUserId($this->user->id));

        $this->assertTrue($this->user->hasSavedTrack($this->track->id));

        $this->event('accounts.saves.track.removed', [
            'userId' => $this->user->id,
            'trackId' => $this->track->id,
        ]);
        $this->replay();

        $this->assertFalse($this->user->hasSavedTrack($this->track->id));
    }
}
