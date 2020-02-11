<?php declare(strict_types=1);

namespace App\Queries;

use App\Entities\Album;
use App\Entities\Reciter;
use Doctrine\Common\Collections\Collection;

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
        $this->builder->andWhere($this->builder->expr()->orX(
            $this->builder->expr()->eq('t.id', ':identifier'),
            $this->builder->expr()->eq('t.year', ':identifier')
        ))->setParameter(':identifier', $identifier);

        return $this;
    }

    protected static function entity(): string
    {
        return Album::class;
    }
}
