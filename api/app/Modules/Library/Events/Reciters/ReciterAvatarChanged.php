<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use Spatie\EventSourcing\ShouldBeStored;

class ReciterAvatarChanged implements ShouldBeStored
{
    public string $id;
    public ?string $avatar;

    public function __construct(string $id, ?string $avatar)
    {
        $this->id = $id;
        $this->avatar = $avatar;
    }
}