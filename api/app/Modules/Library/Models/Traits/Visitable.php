<?php

declare(strict_types=1);

namespace App\Modules\Library\Models\Traits;

use App\Modules\Library\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Visitable
{
    /**
     * Setting relationship
     */
    public function visits(): MorphMany
    {
        return $this->morphMany(Visit::class, 'visitable');
    }

    /**
     * Return count of the visits in the last day
     */
    public function visitsDay(): int
    {
        return $this->visitsLast(1);
    }

    /**
     * Return count of the visits in the last 7 days
     */
    public function visitsWeek(): int
    {
        return $this->visitsLast(7);
    }

    /**
     * Return count of the visits in the last 30 days
     */
    public function visitsMonth(): int
    {
        return $this->visitsLast(30);
    }

    /**
     * Return the count of visits since system was installed
     */
    public function visitsForever(): int
    {
        return $this->visits()
            ->count();
    }

    /**
     * Filter by popular in the last $days days
     */
    public function scopePopularLast(Builder $query, int $days): Builder
    {
        return $this->queryPopularLast($query, $days);
    }

    /**
     * Filter by popular in the last day
     */
    public function scopePopularDay(Builder $query): Builder
    {
        return $this->queryPopularLast($query, 1);
    }

    /**
     * Filter by popular in the last 7 days
     */
    public function scopePopularWeek(Builder $query): Builder
    {
        return $this->queryPopularLast($query, 7);
    }

    /**
     * Filter by popular in the last 30 days
     */
    public function scopePopularMonth(Builder $query): Builder
    {
        return $this->queryPopularLast($query, 30);
    }

    /**
     * Filter by popular in the last 365 days
     */
    public function scopePopularYear(Builder $query): Builder
    {
        return $this->queryPopularLast($query, 365);
    }

    /**
     * Filter by popular in all time
     */
    public function scopePopularAllTime(Builder $query): Builder
    {
        return $query->withCount('visits')->orderBy('visits_count', 'desc');
    }

    /**
     * Return the visits of the model in the last ($days) days
     */
    public function visitsLast(int $days): int
    {
        return $this->visits()
            ->where('visited_at', '>=', Carbon::now()->subDays($days)->toDateString())
            ->count();
    }

    /**
     * Returns a Query Builder with Model ordered by popularity in the Last ($days) days
     */
    public function queryPopularLast(Builder $query, int $days): Builder
    {
        return $query->withCount([
            'visits' => fn (Builder $query) => $query->where('visited_at', '>=', Carbon::now()
                ->subDays($days)
                ->toDateString()),
        ])->orderBy('visits_count', 'desc');
    }
}
