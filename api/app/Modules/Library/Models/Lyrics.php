<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Library\Events\Lyrics\LyricsChanged;
use App\Modules\Library\Events\Lyrics\LyricsCreated;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class Lyrics extends Model implements TimestampedEntity
{
    protected $keyType = 'string';
    protected $guarded = [];

    public static function create(Track $track, string $content, int $format)
    {
        $id = Uuid::uuid1()->toString();

        event(new LyricsCreated($id, $track->id, $content, $format));
    }

    public static function retrieve(string $id): self
    {
        if (!Uuid::isValid($id)) {
            throw new InvalidArgumentException('Invalid UUID.');
        }
        /** @var self $model */
        $model = self::query()->findOrFail($id);

        return $model;
    }

    public function changeLyrics(string $content, ?int $format = null): void
    {
        if ($content !== $this->content) {
            event(new LyricsChanged($this->id, $this->track_id, $content, $format ?? $this->format));
        }
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->created_at);
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->updated_at);
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }
}
