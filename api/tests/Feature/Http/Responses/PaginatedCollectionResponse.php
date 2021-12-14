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

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertPage(int $page): self
    {
        $this->response->assertJsonPath('meta.pagination.current_page', $page);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertTotalPages(int $pages): self
    {
        $this->response->assertJsonPath('meta.pagination.total_pages', $pages);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertTotal(int $total): self
    {
        $this->response->assertJsonPath('meta.pagination.total', $total);

        return $this;
    }
}
