<?php

declare(strict_types=1);

namespace Tests;

trait SetUpCustomTraits
{
    /**
     * @param array<class-string,int> $uses
     */
    public function setUpCustomTraits(array $uses): void
    {
        if (isset($uses[WithSearchIndex::class]) && method_exists($this, 'setUpSearchIndex')) {
            $this->setUpSearchIndex();
        }

        if (isset($uses[DatabaseMigrations::class]) && method_exists($this, 'runDatabaseMigrations')) {
            $this->runDatabaseMigrations();
        }
    }
}
