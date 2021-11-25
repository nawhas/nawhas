<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\Feature\FeatureTest;
use Tests\Feature\Http\Responses\PaginatedCollectionResponse;

use function App\Support\times;

class RecitersTest extends FeatureTest
{
    private const ROUTE_GET_RECITERS = 'v1/reciters';

    /**
     * @test
     */
    public function it_retrieves_empty_list_of_reciters_when_no_reciters_exist(): void
    {
        PaginatedCollectionResponse::from($this->getJson(self::ROUTE_GET_RECITERS))
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
        times(2, fn () => $factory->create());

        PaginatedCollectionResponse::from($this->getJson(self::ROUTE_GET_RECITERS))
            ->assertSuccessful()
            ->assertPage(1)
            ->assertTotal(2)
            ->assertTotalPages(1);
    }
}
