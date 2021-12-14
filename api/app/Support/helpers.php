<?php declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Collection;

function times(int $count, callable $callable): Collection
{
    $results = collect();

    for ($i = 0; $i < $count; $i++) {
        $results->push($callable($i + 1));
    }

    return $results;
}
