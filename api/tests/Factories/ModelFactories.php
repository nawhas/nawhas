<?php

declare(strict_types=1);

namespace Tests\Factories;

trait ModelFactories
{
    protected function getUserFactory(): UserFactory
    {
        return app(UserFactory::class);
    }

    protected function getReciterFactory(): ReciterFactory
    {
        return app(ReciterFactory::class);
    }

    protected function getAlbumFactory(): AlbumFactory
    {
        return app(AlbumFactory::class);
    }

    protected function getTrackFactory(): TrackFactory
    {
        return app(TrackFactory::class);
    }

    protected function getDraftLyricsFactory(): DraftLyricsFactory
    {
        return app(DraftLyricsFactory::class);
    }
}
