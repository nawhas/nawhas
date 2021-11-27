<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class Response
{
    protected TestResponse $response;

    public function __construct(TestResponse $response)
    {
        $this->response = $response;
    }

    /**
     * TODO:PHP8 - Replace return type hint with static
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

    /**
     * TODO:PHP8 - Replace return type hint with static
     * @return static
     */
    public function assertSuccessful(): self
    {
        $this->response->assertSuccessful();
        $this->response->assertJsonStructure($this->getJsonStructure());

        return $this;
    }

    abstract protected function getJsonStructure(): array;

    /**
     * TODO:PHP8 replace self with static
     */
    public static function getItemFactory(): callable
    {
        return static function (array $item, TestResponse $original): self {
            $body = json_encode($item);
            $status = $original->status();
            $headers = $original->headers->all();
            $response = new HttpResponse($body, $status, $headers);

            return static::from(new TestResponse($response));
        };
    }
}
