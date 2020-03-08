<?php

declare(strict_types=1);

namespace App\Visits;

use App\Visits\Entities\Visit;

interface Visitable
{
    public function visit(): Visit;
}
