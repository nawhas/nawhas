<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Library\Models\Reciter;
use Tests\Feature\FeatureTest;
use Tests\Feature\Http\Responses\PaginatedCollectionResponse;
use Tests\Feature\Http\Responses\ReciterResponse;
use Tests\WithSearchIndex;

use Tests\WithSimpleFaker;

use function App\Support\times;

class RecitersTest extends HttpTest
{
    use WithSearchIndex;
    use WithSimpleFaker;

    private const ROUTE_LIST_RECITERS = 'v1/reciters';
    private const ROUTE_CREATE_RECITER = 'v1/reciters';
    private const ROUTE_SHOW_RECITER = 'v1/reciters/%s';
    private const ROUTE_EDIT_RECITER = 'v1/reciters/%s';
    private const ROUTE_DELETE_RECITER = 'v1/reciters/%s';

    /**
     * @test
     */
    public function it_retrieves_empty_list_of_reciters_when_no_reciters_exist(): void
    {
        $response = $this->url(self::ROUTE_LIST_RECITERS)->get();

        PaginatedCollectionResponse::from($response)
            ->assertSuccessful()
            ->assertPage(1)
            ->assertTotal(0);
    }

    /**
     * @test
     */
    public function it_retrieves_paginated_list_of_reciters(): void
    {
        $factory = $this->getReciterFactory();

        // Create two reciters
        /** @var Reciter[] $reciters */
        $reciters = times(2, fn() => $factory->create())->all();
        [$r1, $r2] = $reciters;

        $response = $this->url(self::ROUTE_LIST_RECITERS)->get();

        PaginatedCollectionResponse::from($response)
            ->withItemFactory(ReciterResponse::getItemFactory())
            ->assertSuccessful()
            ->assertPage(1)
            ->assertTotal(2)
            ->assertTotalPages(1)
            ->items(fn(ReciterResponse $item) => $item->assertSuccessful())
            ->item($r1->id, fn(ReciterResponse $reciter) => $reciter->assertMatches($r1))
            ->item($r2->id, fn(ReciterResponse $reciter) => $reciter->assertMatches($r2));
    }

    /**
     * @test
     */
    public function it_retrieves_a_single_reciter_by_id_or_slug(): void
    {
        $reciter = $this->getReciterFactory()->create();

        $response = $this->url(self::ROUTE_SHOW_RECITER, $reciter->id)->get();
        ReciterResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($reciter);

        $response = $this->url(self::ROUTE_SHOW_RECITER, $reciter->slug)->get();
        ReciterResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($reciter);
    }

    /**
     * @test
     */
    public function it_can_create_reciter_if_moderator(): void
    {
        $request = [
            'name' => $this->faker->name,
        ];

        $response = $this->assertRequiresModeratorAuthentication(
            fn () => $this->url(self::ROUTE_CREATE_RECITER)->post($request)
        );

        ReciterResponse::from($response)
            ->assertSuccessful()
            ->assertName($request['name']);

        $this->assertNotNull(Reciter::find($response->json('id')));
    }

    /**
     * @test
     */
    public function it_can_update_reciter_if_moderator(): void
    {
        $reciter = $this->getReciterFactory()->create();

        $request = [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
        ];

        $this->assertNotEquals($reciter->name, $request['name']);
        $this->assertNotEquals($reciter->description, $request['description']);

        $response = $this->assertRequiresModeratorAuthentication(
            fn () => $this->url(self::ROUTE_EDIT_RECITER, $reciter->id)
                ->patch($request)
        );

        ReciterResponse::from($response)
            ->assertSuccessful()
            ->assertName($request['name'])
            ->assertDescription($request['description'])
            ->assertId($reciter->id);

        $reciter->refresh();

        $this->assertEquals($request['name'], $reciter->name);
        $this->assertEquals($request['description'], $reciter->description);
    }

    /**
     * @test
     */
    public function it_requires_reciter_name_to_be_unique_on_create(): void
    {
        $this->getReciterFactory()->create([
            'name' => 'Nadeem Sarwar',
        ]);

        $this->asModerator()
            ->url(self::ROUTE_CREATE_RECITER)
            ->post([
                'name' => 'Nadeem Sarwar'
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'taken']);
    }

    /**
     * @test
     */
    public function it_requires_reciter_name_to_be_unique_on_update(): void
    {
        $existing = $this->getReciterFactory()->create([
            'name' => 'Nadeem Sarwar',
        ]);

        $subject = $this->getReciterFactory()->create([
            'name' => 'Irfan Haider',
        ]);

        $this->asModerator()
            ->url(self::ROUTE_EDIT_RECITER, $subject->id)
            ->patch([
                'name' => 'Nadeem Sarwar'
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'taken']);
    }

    /**
     * @test
     */
    public function it_can_delete_reciter_if_moderator(): void
    {
        $reciter = $this->getReciterFactory()->create();

        $this->assertNotNull(Reciter::find($reciter->id));

        $this->assertRequiresModeratorAuthentication(
            fn () => $this->url(self::ROUTE_DELETE_RECITER, $reciter->id)
                ->delete()
        );

        $this->assertNull(Reciter::find($reciter->id));
    }
}
