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
 * @property string $entity_type
 * @property string $entity_id
 * @property array $old_values
 * @property array $new_values
 * @property string $change_type
 * @property string|null $user_id
 * @property int $version
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $entity
 * @property-read \App\Modules\Authentication\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Audit\Models\Revision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Audit\Models\Revision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Audit\Models\Revision query()
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

    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    public function hasDiff(): bool
    {
        if ($this->change_type === ChangeType::MODIFIED) {
            return json_encode($this->old_values) !== json_encode($this->new_values);
        }

        return true;
    }
}
