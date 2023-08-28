<?php

declare(strict_types=1);

namespace Tests\Feature;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\DatabaseMigrations;
use Tests\Factories\ModelFactories;
use Tests\Feature\Http\Requests\RequestBuilder;
use Tests\TestCase;
use Tests\WithSearchIndex;
use Tests\WithSimpleFaker;

abstract class FeatureTest extends TestCase
{
    use ModelFactories;
    use DatabaseTransactions;

    protected function request(?string $url = null, ...$args): RequestBuilder
    {
        $builder = new RequestBuilder($this);

        if ($url !== null) {
            $builder->url($url, ...$args);
        }

        return $builder;
    }

    protected function url(string $url, string ...$args): RequestBuilder
    {
        return $this->request($url, ...$args);
    }
}
