<?php

namespace App;

use App\Scopes\DefaultSortScope;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    const DEFAULT_ARTWORK_URL = 'https://s3.us-east-2.amazonaws.com/nawhas/defaults/album.png';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new DefaultSortScope('year', 'desc'));
    }

    public function reciter()
    {
        return $this->belongsTo(Reciter::class, 'reciter_id', 'id');
    }

    public function tracks()
    {
        return $this->hasMany(Track::class, 'album_id', 'id');
    }
}
