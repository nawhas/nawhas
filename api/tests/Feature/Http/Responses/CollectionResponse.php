<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert as PHPUnit;

class CollectionResponse extends Response
{
    private ?Closure $itemFactory = null;

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function withItemFactory(callable $itemFactory): self
    {
        $this->itemFactory = Closure::fromCallable($itemFactory);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertNotEmpty(): self
    {
        PHPUnit::assertNotEmpty($this->getData());

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertEmpty(): self
    {
        PHPUnit::assertCount(0, $this->getData());

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function items(callable $callable): self
    {
        $this->getData()
            ->map(fn (array $item) => ($this->itemFactory)($item, $this->response))
            ->each($callable);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function where(callable $finder, callable $assertions, ?int $count = null): self
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

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function item(string $id, callable $assertions): self
    {
        $this->where(fn ($d) => $d['id'] === $id, $assertions, 1);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertHasItem(string $id): self
    {
        $this->where(fn ($d) => $d['id'] === $id, fn () => null, 1);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertDoesNotHaveItem(string $id): self
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
