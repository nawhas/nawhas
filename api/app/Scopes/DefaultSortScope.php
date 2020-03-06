<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DefaultSortScope implements Scope
{
    /** @var string */
    private $column = 'name';

    /** @var string */
    private $direction = 'asc';

    /**
     * DefaultSortScope constructor.
     *
     * @param string $column
     * @param string $direction
     */
    public function __construct(string $column = null, string $direction = null)
    {
        $this->column = $column ?: $this->column;
        $this->direction = $direction ?: $this->direction;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->orderBy($this->column, $this->direction);
    }
}
