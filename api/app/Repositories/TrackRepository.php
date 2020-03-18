<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\{Album, Reciter, Track};
use App\Queries\TrackQuery;
use Illuminate\Support\Collection;

interface TrackRepository
{
    public function find(string $id): ?Track;
    public function get(string $id): Track;
    public function getFromAlbum(Album $album, string $id): Track;
    public function allFromAlbum(Album $album): Collection;
    public function query(): TrackQuery;
    public function persist(Track ...$tracks): void;
    public function remove(Track $track): void;
}
