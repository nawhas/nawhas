<?php

declare(strict_types=1);

namespace App\Modules\Library\Models\Visits;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Library\Models\Visits\TrackStatistic
 *
 * @property string $track_id
 * @property int $visits_all_time
 * @method static \Illuminate\Database\Eloquent\Builder|TrackStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackStatistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackStatistic query()
 * @mixin \Eloquent
 */
class TrackStatistic extends Model
{
    protected $table = 'track_statistics';

    protected $primaryKey = 'track_id';

    public $timestamps = false;

    protected $fillable = [
        'track_id',
        'visits_all_time',
    ];

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
