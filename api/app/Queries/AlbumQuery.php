<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\Album;
use App\Entities\Reciter;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * @method Album|null first()
 * @method Album get()
 * @method Collection|Album[] all()
 */
class AlbumQuery extends Query
{
    public function whereReciter(Reciter $reciter): self
    {
        $this->builder->andWhere('t.reciter = :reciter')
            ->setParameter('reciter', $reciter);

        return $this;
    }

    public function whereIdentifier($identifier): self
    {
        if (Uuid::isValid($identifier)) {
            $this->builder->andWhere('t.id = :id');
        } else {
            $this->builder->andWhere('t.year = :id');
        }

        $this->builder->setParameter(':id', $identifier);

        return $this;
    }

    public function sortByNewest(): self
    {
        $this->builder->orderBy('t.year', 'desc');

        return $this;
    }

    protected static function entity(): string
    {
        return Album::class;
    }
}
