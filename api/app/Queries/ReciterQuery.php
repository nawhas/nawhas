<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\Reciter;
use Illuminate\Support\Collection;

/**
 * @method Reciter get()
 * @method Reciter|null first()
 * @method Collection|Reciter[] all()
 */
class ReciterQuery extends Query
{
    public function whereIdentifier($identifier): self
    {
        $this->builder->andWhere($this->builder->expr()->orX(
            $this->builder->expr()->eq('t.id', ':identifier'),
            $this->builder->expr()->eq('t.slug', ':identifier')
        ))->setParameter(':identifier', $identifier);

        return $this;
    }

    protected static function entity(): string
    {
        return Reciter::class;
    }
}
