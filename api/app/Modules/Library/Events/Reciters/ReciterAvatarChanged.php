<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use App\Modules\Library\Events\UserAction;

class ReciterAvatarChanged extends UserAction
{
    public string $id;
    public ?string $avatar;

    public function __construct(string $id, ?string $avatar)
    {
        $this->id = $id;
        $this->avatar = $avatar;
    }
}
