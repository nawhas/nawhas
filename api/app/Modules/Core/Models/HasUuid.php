<?php

declare(strict_types=1);

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @property-read string $id
 */
trait HasUuid
{
    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model) {
            if ($model->getAttribute('id') === null) {
                $model->setAttribute('id', Uuid::uuid1()->toString());
            }
        });
    }
}
