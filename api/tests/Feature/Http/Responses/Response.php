<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use Illuminate\Testing\TestResponse;

abstract class Response
{
    protected TestResponse $response;

    public function __construct(TestResponse $response)
    {
        $this->response = $response;
    }

    /**
     * TODO:PHP8 - Replace return type hint with static
     * @param TestResponse $response
     * @return static
     */
    public static function from(TestResponse $response): self
    {
        return new static($response);
    }

    public function getTestResponse(): TestResponse
    {
        return $this->response;
    }

    // TODO:PHP8 - Replace return type hint with static
    public function assertSuccessful(): self
    {
        $this->response->assertSuccessful();
        $this->response->assertJsonStructure($this->getJsonStructure());

        return $this;
    }

    abstract protected function getJsonStructure(): array;
}
