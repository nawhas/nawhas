<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

class PaginatedCollectionResponse extends CollectionResponse
{
    protected function getJsonStructure(): array
    {
        return [
            'data',
            'meta' => [
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                    'links'
                ]
            ]
        ];
    }

    public function assertPage(int $page): static
    {
        $this->response->assertJsonPath('meta.pagination.current_page', $page);

        return $this;
    }

    public function assertTotalPages(int $pages): static
    {
        $this->response->assertJsonPath('meta.pagination.total_pages', $pages);

        return $this;
    }

    public function assertTotal(int $total): static
    {
        $this->response->assertJsonPath('meta.pagination.total', $total);

        return $this;
    }
}
