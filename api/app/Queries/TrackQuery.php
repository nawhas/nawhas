<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\{Album, Track};
use Illuminate\Support\Collection;

/**
 * @method Track get()
 * @method Track|null first()
 * @method Collection|Track[] all()
 */
class TrackQuery extends Query
{
    public function whereAlbum(Album $album): self
    {
        $this->builder->andWhere('t.album = :album')
            ->setParameter('album', $album);

        return $this;
    }

    protected static function entity(): string
    {
        return Track::class;
    }
}
