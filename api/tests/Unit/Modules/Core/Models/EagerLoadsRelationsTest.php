<?php

namespace Tests\Unit\Modules\Core\Models;

use App\Modules\Library\Models\Track;
use Tests\TestCase;

class EagerLoadsRelationsTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_only_defined_eager_loadable_relations_recursively()
    {
        $track = new Track();
        $includes = 'reciter,lyrics,media,album,album.tracks,album.tracks.reciter,album.tracks.lyrics,album.tracks.media,album.tracks.album,album.tracks.related';

        $this->assertEqualsCanonicalizing([
            'reciter',
            'album',
            'album.tracks',
            'album.tracks.reciter',
            'album.tracks.album',
        ], $track->getEagerLoadableRelationsFromIncludes($includes));
    }
}
