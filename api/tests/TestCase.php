<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use SetUpCustomTraits;

    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        $this->setUpCustomTraits($uses);

        return $uses;
    }
}
