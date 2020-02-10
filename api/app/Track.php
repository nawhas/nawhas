<?php

namespace App;

use App\Scopes\DefaultSortScope;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new DefaultSortScope('number', 'asc'));
    }

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'id');
    }

    public function reciter()
    {
        return $this->belongsTo(Reciter::class, 'reciter_id', 'id');
    }

    public function lyrics()
    {
        return $this->hasMany(Lyric::class, 'track_id', 'id');
    }

    public function language()
    {
        return $this->belongsToMany('App\Language', 'track_languages', 'track_id', 'language_id');
    }
}
