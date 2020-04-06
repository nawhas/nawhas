<?php

declare(strict_types=1);

namespace App\Values\Lyrics\Documents\JsonV1;

use Illuminate\Support\Collection;

class Lyrics
{
    private Collection $groups;

    public function __construct()
    {
        $this->groups = collect();
    }

    public function addGroup(Group $group): self
    {
        $this->groups->add($group);

        return $this;
    }

    public function getGroups(): array
    {
        return $this->groups->toArray();
    }

    public function toArray(): array
    {
        return $this->groups->map(fn(Group $g) => $g->toArray())->toArray();
    }
}
