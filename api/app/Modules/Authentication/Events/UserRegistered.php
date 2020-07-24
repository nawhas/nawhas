<?php


namespace App\Modules\Authentication\Events;


use Spatie\EventSourcing\ShouldBeStored;

class UserRegistered implements ShouldBeStored
{
    public string $id;
    public array $attributes = [];

    public function __construct(string $id, array $attributes)
    {
        $this->id = $id;
        $this->attributes = $attributes;
    }
}
