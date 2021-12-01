<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Requests;

use Illuminate\Testing\TestResponse;
use Tests\Feature\FeatureTest;

class RequestBuilder
{
    private ?string $url = null;

    private FeatureTest $test;

    public function __construct(FeatureTest $test)
    {
        $this->test = $test;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * TODO:PHP8 - Add mixed type hint
     * @return static
     */
    public function url(string $url, ...$params): self
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

        return $this->test->postJson($this->url);
    }

    public function put(array $data = []): TestResponse
    {
        assert($this->url !== null, 'A url must be provided before calling RequestBuilder::put().');

        return $this->test->putJson($this->url);
    }

    public function patch(array $data = []): TestResponse
    {
        assert($this->url !== null, 'A url must be provided before calling RequestBuilder::patch().');

        return $this->test->patchJson($this->url);
    }

    public function delete(): TestResponse
    {
        assert($this->url !== null, 'A url must be provided before calling RequestBuilder::delete().');

        return $this->test->deleteJson($this->url);
    }
}
