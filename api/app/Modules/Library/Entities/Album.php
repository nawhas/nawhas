<?php

declare(strict_types=1);

namespace App\Modules\Library\Entities;

use Illuminate\Support\Collection;

class Album
{
    public string $id;
    public string $title;
    public string $year;
    public ?string $artwork;

    public Collection $tracks;

    public function __construct(
        string $id,
        string $title,
        string $year,
        ?string $artwork = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->year = $year;
        $this->artwork = $artwork;
        $this->tracks = collect();
    }
}
