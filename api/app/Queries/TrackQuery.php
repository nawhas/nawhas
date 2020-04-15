<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\{Album, Reciter, Track};
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * @method Track get()
 * @method Track|null first()
 * @method Collection<Track> all()
 */
class TrackQuery extends Query
{
    public function whereAlbum(Album $album): self
    {
        $this->builder->andWhere('t.album = :album')
            ->setParameter('album', $album);

        return $this;
    }

    public function whereReciter(Reciter $reciter): self
    {
        $this->builder->andWhere('t.reciter = :reciter')
            ->setParameter('reciter', $reciter);

        return $this;
    }

    public function whereTitle(string $title): self
    {
        $this->builder->andWhere('t.title = :title')
            ->setParameter('title', $title);

        return $this;
    }

    public function whereIdentifier($identifier): self
    {
        if (Uuid::isValid($identifier)) {
            $this->builder->andWhere('t.id = :id');
        } else {
            $this->builder->andWhere('t.slug = :id');
        }

        $this->builder->setParameter(':id', $identifier);

        return $this;
    }

    protected static function entity(): string
    {
        return Track::class;
    }
}
