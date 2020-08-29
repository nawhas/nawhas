<?php

declare(strict_types=1);

namespace App\Modules\Audit\Models;

use App\Modules\Audit\Enum\ChangeType;
use App\Modules\Audit\Snapshots\Snapshot;
use App\Modules\Authentication\Models\User;
use App\Modules\Core\Models\HasUuid;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
 * @property \Illuminate\Support\Carbon $created_at
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

    public static function getLast(
        string $entityType,
        string $entityId
    ): ?Revision {
        return self::query()
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderByDesc('version')
            ->first();
    }

    public static function makeInitial(
        Snapshot $snapshot,
        ChangeType $changeType,
        ?string $userId = null,
        ?DateTimeInterface $timestamp = null
    ): self {
        $revision = new self();
        $revision->entity_type = $snapshot->getType();
        $revision->entity_id = $snapshot->getId();
        $revision->version = 1;
        $revision->user_id = $userId;
        $revision->change_type = $changeType->getValue();
        $revision->created_at = Carbon::make($timestamp ?? 'now');
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
        ?DateTimeInterface $timestamp = null
    ): self {
        $revision = $this->replicate();

        $revision->user_id = $userId;
        $revision->change_type = $changeType->getValue();
        $revision->version = $this->version + 1;
        $revision->created_at = Carbon::make($timestamp ?? 'now');
        $revision->setValues(
            $old = $this->new_values,
            $snapshot->toArray(),
        );

        return $revision;
    }

    public function reviseForDeletion(
        ?string $userId = null,
        ?DateTimeInterface $timestamp = null
    ): self  {
        $revision = $this->replicate();

        $revision->user_id = $userId;
        $revision->change_type = ChangeType::DELETED;
        $revision->version = $this->version + 1;
        $revision->created_at = Carbon::make($timestamp ?? 'now');
        $revision->setValues(
            $old = $this->new_values,
            $new = []
        );

        return $revision;
    }

    protected function setValues(array $old, array $new): void
    {
        $this->old_values = collect($old)->filter(
            fn ($value, $key) => $value !== ($new[$key] ?? null)
        )->all();
        $this->new_values = $new;
    }
}