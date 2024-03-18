<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents\JsonV1;

use Illuminate\Support\Collection;

class Lyrics
{
    /** @var \Illuminate\Support\Collection<int, Group> */
    private Collection $groups;

    public function __construct()
    {
        $this->groups = collect();
    }

    public function addGroup(Group $group): static
    {
        $this->groups->add($group);

        return $this;
    }

    public function getGroups(): array
    {
        return $this->groups->toArray();
    }

    public function render(): string
    {
        return $this->groups->map(fn(Group $group) => $group->render())->join("\n");
    }

    public function toArray(): array
    {
        return $this->groups->map(fn(Group $group) => $group->toArray())->toArray();
    }
}
