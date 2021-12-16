<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Library\Models\Reciter;
use Ramsey\Uuid\Uuid;

class ReciterEventsTest extends EventsTest
{
    /**
     * @test
     */
    public function it_can_replay_reciter_created_event(): void
    {
        $id = Uuid::uuid1()->toString();
        $properties = [
            'id' => $id,
            'attributes' => [
                'name' => $this->faker->name,
                'description' => $this->faker->text,
                'avatar' => $this->faker->imageUrl,
            ]
        ];
        $this->event('reciter.created', $properties);

        $this->replay();

        $reciter = Reciter::find($id);
        $this->assertNotNull($reciter);

        $attributes = $properties['attributes'];
        $this->assertEquals($attributes['name'], $reciter->name);
        $this->assertEquals($attributes['description'], $reciter->description);
        $this->assertEquals($attributes['avatar'], $reciter->avatar);
    }

    /**
     * @test
     */
    public function it_can_replay_reciter_name_changed_event(): void
    {
        $reciter = $this->getReciterFactory()->create();
        $name = $this->faker->name;

        $this->assertNotEquals($reciter->name, $name);

        $this->event('reciter.changed.name', [
            'id' => $reciter->id,
            'name' => $name,
        ]);

        $this->replay();

        $reciter->refresh();
        $this->assertEquals($name, $reciter->name);
    }

    /**
     * @test
     */
    public function it_can_replay_reciter_avatar_changed_event(): void
    {
        $reciter = $this->getReciterFactory()->create();

        // With a real image URL
        $avatar = $this->faker->imageUrl;
        $this->assertNotEquals($reciter->avatar, $avatar);

        $this->event('reciter.changed.avatar', [
            'id' => $reciter->id,
            'avatar' => $avatar,
        ]);

        $this->replay();
        $reciter->refresh();

        $this->assertEquals($avatar, $reciter->avatar);

        // With null
        $this->event('reciter.changed.avatar', [
            'id' => $reciter->id,
            'avatar' => null,
        ]);

        $this->replay();
        $reciter->refresh();

        $this->assertNull($reciter->avatar);
    }

    /**
     * @test
     */
    public function it_can_replay_reciter_viewed_event(): void
    {
        $reciter = $this->getReciterFactory()->create();
        $this->assertEquals(0, $reciter->visitsForever());

        $this->event('reciter.viewed', [
            'id' => $reciter->id,
            'visited_at' => now(),
            'userId' => null,
        ]);

        $this->replay();
        $reciter->refresh();

        $this->assertEquals(1, $reciter->visitsForever());
    }
}
