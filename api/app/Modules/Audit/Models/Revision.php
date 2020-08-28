<?php

declare(strict_types=1);

namespace App\Modules\Audit\Models;

use App\Modules\Audit\Enum\ChangeType;
use App\Modules\Authentication\Models\User;
use App\Modules\Core\Models\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Modules\Audit\Models\Revision
 *
 * @property string $id
 * @property string $aggregate_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string $change_type
 * @property string|null $user_id
 * @property int $version
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read Model|\Eloquent $entity
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Revision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Revision query()
 * @mixin \Eloquent
 */
class Revision extends Model
{
    use HasUuid;

    public $timestamps = false;

    protected $dates = [
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasDiff(): bool
    {
        if ($this->change_type === ChangeType::MODIFIED) {
            return json_encode($this->old_values) !== json_encode($this->new_values);
        }

        return true;
    }
}
