<?php declare(strict_types=1);

namespace App\Support;

use App\Modules\Library\Models\Album;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;

function times(int $count, callable $callable): Collection
{
    $results = collect();

    for ($i = 0; $i < $count; $i++) {
        $results->push($callable($i + 1));
    }

    return $results;
}
