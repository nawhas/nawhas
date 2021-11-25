<?php

declare(strict_types=1);

namespace Tests\Feature;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Factories\InteractsWithFactories;
use Tests\TestCase;
use Tests\WithSearchIndex;
use Tests\WithSimpleFaker;

abstract class FeatureTest extends TestCase
{
    use InteractsWithFactories;
    use DatabaseTransactions;

    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[WithSearchIndex::class])) {
            $this->setUpSearchIndex();
        }

        if (isset($uses[WithSimpleFaker::class])) {
            $this->setUpFaker();
        }

        return $uses;
    }
}
