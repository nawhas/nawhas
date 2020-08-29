<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use App\Modules\Audit\Enum\ChangeType;

class ReciterAvatarChanged extends ReciterEvent
{
    public ?string $avatar;

    public function __construct(string $id, ?string $avatar)
    {
        $this->id = $id;
        $this->avatar = $avatar;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::MODIFIED();
    }
}
