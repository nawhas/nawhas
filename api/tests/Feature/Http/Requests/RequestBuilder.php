<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Requests;

use Illuminate\Testing\TestResponse;
use Tests\Feature\FeatureTestCase;

class RequestBuilder
{
    public function __construct(
        private FeatureTestCase $test,
        private ?string $url = null,
    ) {}

    public function url(string $url, mixed ...$params): static
    {
        $this->url = sprintf($url, ...$params);

        return $this;
    }

    public function get(): TestResponse
    {
        assert($this->url !== null, 'A url must be provided before calling RequestBuilder::get().');

        return $this->test->getJson($this->url);
    }

    public function post(array $data = []): TestResponse
    {
        assert($this->url !== null, 'A url must be provided before calling RequestBuilder::post().');

        return $this->test->postJson($this->url, $data);
    }

    public function put(array $data = []): TestResponse
    {
        assert($this->url !== null, 'A url must be provided before calling RequestBuilder::put().');

        return $this->test->putJson($this->url, $data);
    }

    public function patch(array $data = []): TestResponse
    {
        assert($this->url !== null, 'A url must be provided before calling RequestBuilder::patch().');

        return $this->test->patchJson($this->url, $data);
    }

    public function delete(): TestResponse
    {
        assert($this->url !== null, 'A url must be provided before calling RequestBuilder::delete().');

        return $this->test->deleteJson($this->url);
    }
}
