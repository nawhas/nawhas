<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\ShouldBeStored;

class UserNicknameChanged implements ShouldBeStored
{
    public string $id;
    public string $nickname;

    public function __construct(string $id, string $nickname)
    {
        $this->id = $id;
        $this->nickname = $nickname;
    }
}
