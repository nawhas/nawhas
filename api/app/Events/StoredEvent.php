<?php

declare(strict_types=1);

namespace App\Events;

use Spatie\EventSourcing\Models\EloquentStoredEvent;

class StoredEvent extends EloquentStoredEvent
{
    public static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (auth()->check()) {
                $model->meta_data['user_id'] = auth()->user()->getAuthIdentifier();
            }
        });
    }
}
