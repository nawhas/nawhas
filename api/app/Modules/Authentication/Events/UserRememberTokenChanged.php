<?php


namespace App\Modules\Authentication\Events;


use Spatie\EventSourcing\ShouldBeStored;

class UserRememberTokenChanged implements ShouldBeStored
{
    public string $id;
    public bool $rememberToken;

    public function __construct(string $id, bool $rememberToken)
    {
        $this->id = $id;
        $this->rememberToken = $rememberToken;
    }
}
