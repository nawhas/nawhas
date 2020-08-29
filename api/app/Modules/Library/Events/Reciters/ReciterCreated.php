<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use App\Modules\Audit\Enum\ChangeType;

class ReciterCreated extends ReciterEvent
{
    public array $attributes = [];

    public function __construct(string $id, array $attributes)
    {
        $this->id = $id;
        $this->attributes = $attributes;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::CREATED();
    }
}
