<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Reciter;
use App\Entities\Track;
use App\Support\Pagination\PaginationState;
use Illuminate\Support\Collection;

interface PopularEntitiesRepository
{
    /**
     * @return Collection|Reciter[]
     */
    public function reciters(PaginationState $pagination): Collection;

    /**
     * @return Collection|Track[]
     */
    public function tracks(PaginationState $pagination, ?Reciter $reciter = null): Collection;
}
