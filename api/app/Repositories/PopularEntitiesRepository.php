<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Reciter;
use App\Entities\Track;
use Illuminate\Support\Collection;

interface PopularEntitiesRepository
{
    /**
     * @return Collection|Reciter[]
     */
    public function reciters(): Collection;

    /**
     * @return Collection|Track[]
     */
    public function tracks(?Reciter $reciter = null): Collection;
}
