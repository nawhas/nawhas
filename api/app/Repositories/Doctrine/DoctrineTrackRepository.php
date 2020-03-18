<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Queries\TrackQuery;
use App\Repositories\TrackRepository;
use Illuminate\Support\Collection;

/**
 * @method Track|null findFromRepo(string $id)
 * @method Track getFromRepo(string $id)
 */
class DoctrineTrackRepository extends DoctrineRepository implements TrackRepository
{
    public function find(string $id): ?Track
    {
        return $this->findFromRepo($id);
    }

    public function get(string $id): Track
    {
        return $this->getFromRepo($id);
    }

    public function getFromAlbum(Album $album, string $id): Track
    {
        return $this->query()
            ->whereAlbum($album)
            ->whereIdentifier($id)
            ->get();
    }

    public function allFromAlbum(Album $album): Collection
    {
        return $this->query()
            ->whereAlbum($album)
            ->all();
    }

    public function query(): TrackQuery
    {
        return TrackQuery::make();
    }

    protected function entity(): string
    {
        return Track::class;
    }

    public function persist(Track ...$tracks): void
    {
        $this->em->persist(...$tracks);
    }

    public function remove(Track $track): void
    {
        $this->em->remove($track);
    }
}
