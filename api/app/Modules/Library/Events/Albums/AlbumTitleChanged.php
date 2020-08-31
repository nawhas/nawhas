<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

class AlbumTitleChanged extends AlbumEvent
{
    public string $title;

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
