<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\Reciter;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * @method Reciter get()
 * @method Reciter|null first()
 * @method Collection|Reciter[] all()
 */
class ReciterQuery extends Query
{
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

    public function whereName(string $name): self
    {
        $this->builder->andWhere('t.name = :name')
            ->setParameter('name', $name);

        return $this;
    }

    public function popular(): self
    {
        $this->builder
            ->where($this->builder->expr()->in('t.slug', [
                'nadeem-sarwar', 'irfan-haider', 'tejani-brothers',
                'farhan-ali-waris', 'hassan-sadiq', 'mir-hasan-mir'
            ]));

        return $this;
    }

    public function sortByName(): self
    {
        $this->builder->orderBy('t.name', 'asc');

        return $this;
    }

    protected static function entity(): string
    {
        return Reciter::class;
    }
}
