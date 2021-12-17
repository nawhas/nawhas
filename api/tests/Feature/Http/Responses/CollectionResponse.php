<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert as PHPUnit;

class CollectionResponse extends Response
{
    private ?Closure $itemFactory = null;

    public function withItemFactory(callable $itemFactory): static
    {
        $this->itemFactory = Closure::fromCallable($itemFactory);

        return $this;
    }

    public function assertNotEmpty(): static
    {
        PHPUnit::assertNotEmpty($this->getData());

        return $this;
    }

    public function assertEmpty(): static
    {
        PHPUnit::assertCount(0, $this->getData());

        return $this;
    }

    public function items(callable $callable): static
    {
        $this->getData()
            ->map(fn (array $item) => ($this->itemFactory)($item, $this->response))
            ->each($callable);

        return $this;
    }

    public function where(callable $finder, callable $assertions, ?int $count = null): static
    {
        $items = $this->getData()->filter($finder);

        if ($count === null) {
            PHPUnit::assertGreaterThan(0, $items->count());
        } else {
            PHPUnit::assertCount($count, $items);
        }

        $items->map(fn (array $item) => ($this->itemFactory)($item, $this->response))
            ->each($assertions);

        return $this;
    }

    public function item(string $id, callable $assertions): static
    {
        $this->where(fn ($d) => $d['id'] === $id, $assertions, 1);

        return $this;
    }

    public function assertHasItem(string $id): static
    {
        $this->where(fn ($d) => $d['id'] === $id, fn () => null, 1);

        return $this;
    }

    public function assertDoesNotHaveItem(string $id): static
    {
        $this->where(fn ($d) => $d['id'] === $id, fn () => null, 0);

        return $this;
    }

    public function getData(): Collection
    {
        $data = $this->response->json('data');

        return collect($data);
    }

    protected function getJsonStructure(): array
    {
        return [
            'data',
            'meta'
        ];
    }
}
