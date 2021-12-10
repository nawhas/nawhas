<?php

declare(strict_types=1);

namespace App\Support\Pagination;

use Illuminate\Http\Request;

class PaginationState
{
    public const DEFAULT_LIMIT = 10;

    private int $page;
    private ?int $limit;

    protected function __construct(int $page, ?int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public static function make(int $page = 1, ?int $limit = null): static
    {
        return new self($page, $limit);
    }

    public static function fromRequest(Request $request): static
    {
        $perPage = $request->get('per_page') ? (int)$request->get('per_page') : null;
        return new self((int)$request->get('page', 1), $perPage);
    }

    public function getLimit(int $default = self::DEFAULT_LIMIT): int
    {
        return $this->limit ?? self::DEFAULT_LIMIT;
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
