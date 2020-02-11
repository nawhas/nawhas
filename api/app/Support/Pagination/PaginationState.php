<?php

declare(strict_types=1);

namespace App\Support\Pagination;

use Illuminate\Http\Request;

class PaginationState
{
    public const DEFAULT_LIMIT = 10;

    private int $page = 1;
    private int $limit = 10;

    protected function __construct(int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public static function make(int $page = 1, int $limit = self::DEFAULT_LIMIT): self
    {
        return new self($page, $limit);
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->get('page', 1), $request->get('per_page', self::DEFAULT_LIMIT));
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
