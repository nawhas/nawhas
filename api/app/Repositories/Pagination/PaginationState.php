<?php

declare(strict_types=1);

namespace App\Repositories\Pagination;

class PaginationState
{
    private int $page = 1;
    private int $limit = 10;

    protected function __construct(int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public static function make(int $page = 1, int $limit = 10): self
    {
        return new self($page, $limit);
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
