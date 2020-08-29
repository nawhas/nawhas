<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use App\Modules\Audit\Enum\ChangeType;

class ReciterDeleted extends ReciterEvent
{
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::MODIFIED();
    }
}
