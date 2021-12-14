<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Authentication\Enum\Role;
use App\Modules\Core\Events\StoredEvent;
use Tests\Feature\FeatureTest;

class ReplayEventsTest extends FeatureTest
{
    protected function setUp(): void
    {
        parent::setUp();

        StoredEvent::truncate();

        StoredEvent::create([
            'event_version' => 1,
            'created_at' => '2020-07-30 22:03:59',
            'event_properties' => [
                'id' => 'a8afb8ca-b9c9-11ea-aa22-3a3d4d50f449',
                'attributes' => [
                    'name' => 'Zain',
                    'role' => Role::MODERATOR,
                    'email' => 'szainmehdi@gmail.com',
                    'password' => bcrypt('secret'),
                    'remember_token' => null,
                    'nickname' => null,
                ],
            ],
            'aggregate_version' => null,
            'aggregate_uuid' => null,
            'meta_data' => [],
            'event_class' => 'user.registered',
        ]);
    }

    /**
     * @test
     */
    public function it_has_one_stored_event_in_database(): void
    {
        $this->assertEquals(1, StoredEvent::query()->count());

        $first = StoredEvent::find(1);
        $this->assertEquals(1, $first->event_version);
    }

    /**
     * @test
     */
    public function it_can_replay_event(): void
    {
        $result = $this->withoutMockingConsoleOutput()
            ->artisan('event-sourcing:replay', ['-n' => true]);
        $this->assertEquals(0, $result);
    }
}
