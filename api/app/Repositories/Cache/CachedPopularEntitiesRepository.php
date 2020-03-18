<?php

declare(strict_types=1);

namespace App\Repositories\Cache;

use App\Database\Doctrine\EntityManager;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Repositories\PopularEntitiesRepository;
use App\Repositories\ReciterRepository;
use App\Repositories\TrackRepository;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;

class CachedPopularEntitiesRepository implements PopularEntitiesRepository
{
    private const TTL = 60 * 30; // 30 minutes
    private PopularEntitiesRepository $wrapped;
    private ReciterRepository $reciterRepo;
    private TrackRepository $trackRepo;
    private Cache $cache;

    public function __construct(
        Cache $cache,
        PopularEntitiesRepository $wrapped,
        ReciterRepository $reciterRepo,
        TrackRepository $trackRepo
    ) {
        $this->wrapped = $wrapped;
        $this->cache = $cache;
        $this->reciterRepo = $reciterRepo;
        $this->trackRepo = $trackRepo;
    }

    public function reciters(): Collection
    {
        $ids = $this->cache->remember(
            'popular.reciters',
            self::TTL,
            fn() => $this->wrapped
                ->reciters()
                ->map(fn(Reciter $reciter) => $reciter->getId())
                ->toArray()
        );

        return $this->reciterRepo->all(...$ids);
    }

    public function tracks(?Reciter $reciter = null): Collection
    {
        $key = $reciter ? "popular.tracks.reciter:{$reciter->getId()}" : 'popular.tracks';

        $ids = $this->cache->remember(
            $key,
            self::TTL,
            fn() => $this->wrapped
                ->tracks($reciter)
                ->map(fn (Track $track) => $track->getId())
                ->toArray()
        );

        return $this->trackRepo->all(...$ids);
    }
}
