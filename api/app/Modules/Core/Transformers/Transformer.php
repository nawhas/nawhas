<?php

namespace App\Modules\Core\Transformers;

use App\Exceptions\NotImplementedException;
use App\Modules\Core\Contracts\TimestampedEntity;
use BadMethodCallException;
use DateTimeInterface;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\MergeValue;
use League\Fractal\Resource\NullResource;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

abstract class Transformer extends TransformerAbstract
{
    use ConditionallyLoadsAttributes;

    public function __invoke($data): array
    {
        if (!method_exists($this, 'toArray')) {
            throw new BadMethodCallException('Transformer must implement `toArray($data): array` method.');
        }

        return $this->filter($this->toArray($data));
    }

    protected function dateTime(?DateTimeInterface $dateTime): ?string
    {
        if (!$dateTime) {
            return null;
        }

        return $dateTime->format(DATE_RFC3339);
    }

    protected function timestamps(TimestampedEntity $entity): MergeValue
    {
        return $this->merge([
            'createdAt' => $this->dateTime($entity->getCreatedAt()),
            'updatedAt' => $this->dateTime($entity->getUpdatedAt()),
        ]);
    }

    protected function null(): NullResource
    {
        throw new NotImplementedException('Use Transformer#empty() instead.');
    }

    protected function empty(): Primitive
    {
        return new Primitive(data: null);
    }
}
