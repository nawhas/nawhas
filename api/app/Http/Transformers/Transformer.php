<?php

namespace App\Http\Transformers;

use App\Entities\Contracts\TimestampedEntity;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\MergeValue;
use League\Fractal\TransformerAbstract;

abstract class Transformer extends TransformerAbstract
{
    use ConditionallyLoadsAttributes;

    public function __invoke($data): array
    {
        if (!method_exists($this, 'toArray')) {
            throw new \BadMethodCallException('Transformer must implement `toArray($data): array` method.');
        }

        return $this->filter($this->toArray($data));
    }

    protected function dateTime(?\DateTimeInterface $dateTime): ?string
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
}
