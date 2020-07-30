<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use App\Modules\Library\Events\UserAction;

class AlbumCreated extends UserAction
{
    public string $id;
    public string $reciterId;
    public array $attributes = [];

    public function __construct(string $id, string $reciterId, array $attributes)
    {
        $this->id = $id;
        $this->reciterId = $reciterId;
        $this->attributes = $attributes;
    }
}
