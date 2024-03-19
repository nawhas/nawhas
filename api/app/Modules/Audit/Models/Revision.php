<?php

declare(strict_types=1);

namespace App\Modules\Audit\Models;

use App\Modules\Audit\Enum\ChangeType;
use App\Modules\Audit\Enum\EntityType;
use App\Modules\Audit\Snapshots\Snapshot;
use App\Modules\Authentication\Models\User;
use App\Modules\Core\Models\HasUuid;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

/**
 * App\Modules\Audit\Models\Revision
 *
 * @property string $id
 * @property string $entity_type
 * @property string $entity_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string $change_type
 * @property string|null $user_id
 * @property int $version
 * @property int $event_id
 * @property \Carbon\Carbon $created_at
 * @property-read null|\App\Modules\Audit\Revisionable\Revisionable|\Illuminate\Database\Eloquent\Model $entity
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
        'created_at' => 'datetime'
    ];

    public function getChangeType(): ChangeType
    {
        return ChangeType::from($this->change_type);
    }

    public function getEntityType(): EntityType
    {
        return EntityType::from($this->entity_type);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    public static function getLast(
        EntityType $entityType,
        string $entityId
    ): ?Revision {
        return self::query()
            ->where('entity_type', $entityType->value)
            ->where('entity_id', $entityId)
            ->orderByDesc('version')
            ->first();
    }

    public static function makeInitial(
        Snapshot $snapshot,
        ChangeType $changeType,
        ?string $userId = null,
        ?StoredEvent $event = null
    ): self {
        $revision = new self();
        $revision->entity_type = $snapshot->getType()->value;
        $revision->entity_id = $snapshot->getId();
        $revision->version = 1;
        $revision->user_id = $userId;
        $revision->change_type = $changeType->value;
        $revision->event_id = $event->id;
        $revision->created_at = Carbon::make($event ? $event->created_at : 'now');
        $revision->setValues(
            $old = [],
            $new = $snapshot->toArray()
        );

        return $revision;
    }

    public function revise(
        Snapshot $snapshot,
        ChangeType $changeType,
        ?string $userId = null,
        ?StoredEvent $event = null
    ): static {
        $revision = $this->replicate();

        $revision->user_id = $userId;
        $revision->change_type = $changeType->value;
        $revision->version = $this->version + 1;
        $revision->created_at = Carbon::make($event ? $event->created_at : 'now');
        $revision->event_id = $event->id;
        $revision->setValues(
            $old = $this->new_values,
            $snapshot->toArray(),
        );

        return $revision;
    }

    public function reviseForDeletion(
        ?string $userId = null,
        ?StoredEvent $event = null
    ): static  {
        $revision = $this->replicate();

        $revision->user_id = $userId;
        $revision->change_type = ChangeType::Deleted->value;
        $revision->version = $this->version + 1;
        $revision->created_at = Carbon::make($event ? $event->created_at : 'now');
        $revision->event_id = $event->id;
        $revision->setValues(
            $old = [],
            $new = $this->new_values,
        );

        return $revision;
    }

    protected function setValues(array $old, array $new): void
    {
        foreach ($new as $key => $value) {
            if (!isset($old[$key])) {
                $old[$key] = null;
            }
        }

        $old = collect($old)->filter(function ($value, $key) use ($new) {
            if (is_array($value)) {
                return json_encode($value) !== json_encode($new[$key] ?? []);
            }
            return $value !== ($new[$key] ?? null);
        })->all();

        $this->old_values = $old;
        $this->new_values = $new;
    }

    public function save(array $options = [])
    {
        if ($this->change_type === ChangeType::Modified->value && empty($this->old_values)) {
            return false;
        }

        return parent::save($options);
    }
}
