<?php

use App\Database\Doctrine\EntityManager;
use App\Entities\Album;
use App\Entities\Lyrics;
use App\Entities\Reciter;
use App\Entities\Track;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(EntityManager $em)
    {
        $reciter = new Reciter('Nadeem Sarwar', 'Top Reciter');
        $album = new Album($reciter, 'Ya Hussain', '2019');
        $track = new Track($album, 'Ya Ali Ya Hussain');

        // Lyrics have to be handled a bit differently.
        // The idea is that many lyrics objects can stand own their
        // own with a reference back to the track they're related to.
        // But the Track object only cares about one lyric object.
        // This association must be made separately.
        $lyrics = new Lyrics($track, 'Hello World');
        $track->replaceLyrics($lyrics);

        $em->persist($reciter, $album, $track, $lyrics);
        app('em')->flush();
    }
}
