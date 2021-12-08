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
use DateTimeInterface;
use Illuminate\Support\Str;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class AliasesProjector extends Projector
{
    public function onReciterCreated(ReciterCreated $event): void
    {
        $this->createReciterAlias($event->id, $event->attributes['name'], $event->createdAt());
    }

    public function onReciterNameChanged(ReciterNameChanged $event): void
    {
        $this->createReciterAlias($event->id, $event->name, $event->createdAt());
    }

    public function onAlbumCreated(AlbumCreated $event): void
    {
        $this->createAlbumAlias($event->reciterId, $event->id, $event->attributes['year'], $event->createdAt());
    }

    public function onAlbumYearChanged(AlbumYearChanged $event): void
    {
        $this->createAlbumAlias(
            $this->getPreviousAlbumAlias($event->id)->reciter_id,
            $event->id,
            $event->year,
            $event->createdAt()
        );
    }

    public function onTrackCreated(TrackCreated $event): void
    {
        $albumAlias = $this->getPreviousAlbumAlias($event->albumId);
        $this->createTrackAlias(
            $albumAlias->reciter_id,
            $event->albumId,
            $event->id,
            $event->attributes['title'],
            $event->createdAt()
        );
    }

    public function onTrackTitleChanged(TrackTitleChanged $event): void
    {
        $trackAlias = $this->getPreviousTrackAlias($event->id);

        $this->createTrackAlias(
            $trackAlias->reciter_id,
            $trackAlias->album_id,
            $event->id,
            $event->title,
            $event->createdAt()
        );
    }

    private function createReciterAlias(string $reciterId, string $alias, DateTimeInterface $timestamp): ReciterAlias
    {
        return ReciterAlias::updateOrCreate([
            'alias' => Str::slug($alias),
        ], [
            'reciter_id' => $reciterId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    private function createAlbumAlias(string $reciterId, string $albumId, string $alias, DateTimeInterface $timestamp): AlbumAlias
    {
        return AlbumAlias::updateOrCreate([
            'reciter_id' => $reciterId,
            'alias' => $alias, // Album years are not slugged right now.
        ], [
            'album_id' => $albumId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    private function createTrackAlias(
        string $reciterId,
        string $albumId,
        string $trackId,
        string $alias,
        DateTimeInterface $timestamp
    ): TrackAlias {
        return TrackAlias::updateOrCreate([
            'reciter_id' => $reciterId,
            'album_id' => $albumId,
            'alias' => Str::slug($alias),
        ], [
            'track_id' => $trackId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
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
