<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use App\Modules\Audit\Enum\ChangeType;

class ReciterNameChanged extends RevisionableReciterEvent
{
    public string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::MODIFIED();
    }
}
