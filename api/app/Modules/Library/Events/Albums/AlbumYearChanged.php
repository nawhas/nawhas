<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

class AlbumYearChanged extends AlbumEvent
{
    public string $year;

    public function __construct(string $id, string $year)
    {
        $this->id = $id;
        $this->year = $year;
    }
}
