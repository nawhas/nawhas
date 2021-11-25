<?php

declare(strict_types=1);

namespace Tests;

trait WithSearchIndex
{
    public function setUpSearchIndex(): void
    {
        $this->artisan('meilisearch:setup', ['--reset' => true]);
    }
}
