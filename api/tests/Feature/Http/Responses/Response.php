<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Testing\TestResponse;

abstract class Response
{
    protected TestResponse $response;

    public function __construct(TestResponse $response)
    {
        $this->response = $response;
    }

    public static function from(TestResponse $response): static
    {
        return new static($response);
    }

    public function getTestResponse(): TestResponse
    {
        return $this->response;
    }

    public function assertSuccessful(): static
    {
        $this->response->assertSuccessful();
        $this->response->assertJsonStructure($this->getJsonStructure());

        return $this;
    }

    abstract protected function getJsonStructure(): array;

    public static function getItemFactory(): callable
    {
        return static function (array $item, TestResponse $original): static {
            $body = json_encode($item);
            $status = $original->status();
            $headers = $original->headers->all();
            $response = new HttpResponse($body, $status, $headers);

            return static::from(new TestResponse($response));
        };
    }
}
