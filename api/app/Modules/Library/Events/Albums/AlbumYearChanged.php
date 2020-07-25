<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AlbumYearChanged extends ShouldBeStored
{
    public string $id;
    public string $year;

    public function __construct(string $id, string $year)
    {
        $this->id = $id;
        $this->year = $year;
    }
}
