<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use App\Modules\Core\Events\UserAction;

class AlbumTitleChanged extends UserAction
{
    public string $id;
    public string $title;

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
