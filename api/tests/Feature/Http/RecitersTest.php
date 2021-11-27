<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Library\Models\Reciter;
use Tests\Feature\FeatureTest;
use Tests\Feature\Http\Responses\PaginatedCollectionResponse;

use Tests\Feature\Http\Responses\ReciterResponse;
use Tests\WithSearchIndex;

use function App\Support\times;

class RecitersTest extends FeatureTest
{
    use WithSearchIndex;

    private const ROUTE_LIST_RECITERS = 'v1/reciters';
    private const ROUTE_SHOW_RECITER = 'v1/reciters';

    /**
     * @test
     */
    public function it_retrieves_empty_list_of_reciters_when_no_reciters_exist(): void
    {
        PaginatedCollectionResponse::from($this->getJson(self::ROUTE_LIST_RECITERS))
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
        $reciters = times(2, fn () => $factory->create())->all();
        [$r1, $r2] = $reciters;

        PaginatedCollectionResponse::from($this->getJson(self::ROUTE_LIST_RECITERS))
            ->withItemFactory(ReciterResponse::getItemFactory())
            ->assertSuccessful()
            ->assertPage(1)
            ->assertTotal(2)
            ->assertTotalPages(1)
            ->items(fn (ReciterResponse $item) => $item->assertSuccessful())
            ->item($r1->id, fn (ReciterResponse $reciter) => $reciter->assertMatches($r1))
            ->item($r2->id, fn (ReciterResponse $reciter) => $reciter->assertMatches($r2));
    }
}
