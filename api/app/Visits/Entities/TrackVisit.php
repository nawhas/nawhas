<?php

declare(strict_types=1);

namespace App\Visits\Entities;

use App\Entities\Track;
use DateTimeInterface;

class TrackVisit extends Visit
{
    private Track $track;

    public function __construct(Track $track, ?DateTimeInterface $date = null)
    {
        parent::__construct($date);
        $this->track = $track;
    }

    public function getTrack(): Track
    {
        return $this->track;
    }
}
