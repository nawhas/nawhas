<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use App\Modules\Audit\Enum\ChangeType;

class ReciterDescriptionChanged extends RevisionableReciterEvent
{
    public ?string $description;

    public function __construct(string $id, ?string $description)
    {
        $this->id = $id;
        $this->description = $description;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::MODIFIED();
    }
}
