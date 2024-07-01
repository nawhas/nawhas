<?php

namespace App\Modules\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|static withIncludes(string|null $includes)
 */
trait EagerLoadsRelations
{
    /**
     * @return array<string, class-string>
     */
    abstract public function getEagerLoadableRelations(): array;

    public function scopeWithIncludes(Builder $query, string|null $includes): Builder
    {
        if ($includes === null) {
            return $query;
        }

        return $query->with($this->getEagerLoadableRelationsFromIncludes($includes));
    }

    public function loadIncludes(string|null $includes): void
    {
        if ($includes === null) {
            return;
        }

        $this->loadMissing($this->getEagerLoadableRelationsFromIncludes($includes));
    }

    public function getEagerLoadableRelationsFromIncludes(string $includes): array
    {
        $relations = explode(',', $includes);

        return array_filter($relations, function (string $include) {
            return $this->isEagerLoadable($this, $include);
        });
    }

    private function isEagerLoadable(object $model, string $include): bool {
        if (!method_exists($model, 'getEagerLoadableRelations')) {
            return false;
        }

        $parts = explode('.', $include);

        $related = $parts[0];
        $definition = $model->getEagerLoadableRelations()[$related] ?? null;

        if ($definition === null) {
            return false;
        }

        if (count($parts) === 1) {
            return true;
        }

        return $this->isEagerLoadable(new $definition(), implode('.', array_slice($parts, 1)));
    }
}
