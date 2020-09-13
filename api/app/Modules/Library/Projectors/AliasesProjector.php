<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\{
    Albums\AlbumCreated,
    Albums\AlbumYearChanged,
    Reciters\ReciterCreated,
    Reciters\ReciterNameChanged,
    Tracks\TrackCreated,
    Tracks\TrackTitleChanged
};
use App\Modules\Library\Models\Aliases\{AlbumAlias, ReciterAlias, TrackAlias};
use Illuminate\Support\Str;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class AliasesProjector extends Projector
{
    public function onReciterCreated(ReciterCreated $event, StoredEvent $storedEvent): void
    {
        $this->createReciterAlias($event->id, $event->attributes['name'], $storedEvent);
    }

    public function onReciterNameChanged(ReciterNameChanged $event, StoredEvent $storedEvent): void
    {
        $this->createReciterAlias($event->id, $event->name, $storedEvent);
    }

    public function onAlbumCreated(AlbumCreated $event, StoredEvent $storedEvent): void
    {
        $this->createAlbumAlias($event->reciterId, $event->id, $event->attributes['year'], $storedEvent);
    }

    public function onAlbumYearChanged(AlbumYearChanged $event, StoredEvent $storedEvent): void
    {
        $this->createAlbumAlias(
            $this->getPreviousAlbumAlias($event->id)->reciter_id,
            $event->id,
            $event->year,
            $storedEvent
        );
    }

    public function onTrackCreated(TrackCreated $event, StoredEvent $storedEvent): void
    {
        $albumAlias = $this->getPreviousAlbumAlias($event->albumId);
        $this->createTrackAlias(
            $albumAlias->reciter_id,
            $event->albumId,
            $event->id,
            $event->attributes['title'],
            $storedEvent
        );
    }

    public function onTrackTitleChanged(TrackTitleChanged $event, StoredEvent $storedEvent): void
    {
        $trackAlias = $this->getPreviousTrackAlias($event->id);

        $this->createTrackAlias(
            $trackAlias->reciter_id,
            $trackAlias->album_id,
            $event->id,
            $event->title,
            $storedEvent
        );
    }

    private function createReciterAlias(string $reciterId, string $alias, StoredEvent $event): ReciterAlias
    {
        return ReciterAlias::updateOrCreate([
            'alias' => Str::slug($alias),
        ], [
            'reciter_id' => $reciterId,
            'created_at' => $event->created_at,
            'updated_at' => $event->created_at,
        ]);
    }

    private function createAlbumAlias(string $reciterId, string $albumId, string $alias, StoredEvent $event): AlbumAlias
    {
        return AlbumAlias::updateOrCreate([
            'reciter_id' => $reciterId,
            'alias' => $alias, // Album years are not slugged right now.
        ], [
            'album_id' => $albumId,
            'created_at' => $event->created_at,
            'updated_at' => $event->created_at,
        ]);
    }

    private function createTrackAlias(
        string $reciterId,
        string $albumId,
        string $trackId,
        string $alias,
        StoredEvent $event
    ): TrackAlias {
        return TrackAlias::updateOrCreate([
            'reciter_id' => $reciterId,
            'album_id' => $albumId,
            'alias' => Str::slug($alias),
        ], [
            'track_id' => $trackId,
            'created_at' => $event->created_at,
            'updated_at' => $event->created_at,
        ]);
    }

    private function getPreviousAlbumAlias(string $id): AlbumAlias
    {
        /** @var AlbumAlias $existing */
        $existing = AlbumAlias::where('album_id', $id)->latest()->firstOrFail();

        return $existing;
    }

    private function getPreviousTrackAlias(string $id): TrackAlias
    {
        /** @var TrackAlias $existing */
        $existing = TrackAlias::where('track_id', $id)->latest()->firstOrFail();

        return $existing;
    }

    public function resetState(): void
    {
        ReciterAlias::truncate();
        AlbumAlias::truncate();
        TrackAlias::truncate();
    }
}
