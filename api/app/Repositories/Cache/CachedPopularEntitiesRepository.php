<?php

declare(strict_types=1);

namespace App\Repositories\Cache;

use App\Entities\Reciter;
use App\Repositories\PopularEntitiesRepository;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;

class CachedPopularEntitiesRepository implements PopularEntitiesRepository
{
    private const TTL = 60 * 30; // 30 minutes
    private PopularEntitiesRepository $wrapped;
    private Cache $cache;

    public function __construct(Cache $cache, PopularEntitiesRepository $wrapped)
    {
        $this->wrapped = $wrapped;
        $this->cache = $cache;
    }

    public function reciters(): Collection
    {
        return $this->cache->remember(
            'popular.reciters',
            self::TTL,
            fn() => $this->wrapped->reciters()
        );
    }

    public function tracks(?Reciter $reciter = null): Collection
    {
        $key = $reciter ? "popular.tracks.reciter:{$reciter->getId()}" : 'popular.tracks';

        return $this->cache->remember(
            $key,
            self::TTL,
            fn() => $this->wrapped->tracks($reciter)
        );
    }
}
