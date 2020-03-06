<?php

declare(strict_types=1);

namespace App\Visits\Entities;

use App\Entities\Reciter;
use DateTimeInterface;

class ReciterVisit extends Visit
{
    private Reciter $reciter;

    public function __construct(Reciter $reciter, ?DateTimeInterface $date = null)
    {
        parent::__construct($date);
        $this->reciter = $reciter;
    }

    public function getReciter(): Reciter
    {
        return $this->reciter;
    }
}
