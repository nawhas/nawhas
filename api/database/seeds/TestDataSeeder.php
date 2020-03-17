<?php

use App\Database\Doctrine\EntityManager;
use App\Entities\Album;
use App\Entities\Lyrics;
use App\Entities\Media;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Entities\User;
use App\Enum\Role;
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
        $existing = $em->repository(User::class)->count([]);

        if ($existing > 0) {
            logger()->debug('Database already seeded. Exiting');
            return;
        }

        $data = file_get_contents(database_path('seeds/seed.json'));

        $mod = new User(Role::MODERATOR(), 'Moderator', 'moderator@nawhas.com', 'secret');
        $contrib = new User(Role::CONTRIBUTOR(), 'Contributor', 'contributor@nawhas.com', 'secret');

        $em->persist($mod, $contrib);

        foreach ($data as $r) {
            $reciter = new Reciter($r['name'], $r['description'], $r['avatar']);
            $em->persist($reciter);

            foreach ($r['albums'] as $a) {
                $album = new Album($reciter, $a['title'], $a['year'], $a['artwork']);
                $em->persist($album);

                foreach ($a['tracks'] as $t) {
                    $track = new Track($album, $t['title']);
                    if ($t['lyrics']) {
                        $track->replaceLyrics(new Lyrics($track, $t['lyrics']));
                    }
                    if ($t['audio']) {
                        $track->addAudioFile(Media::audioFile($t['audio']));
                    }
                    $em->persist($track);
                }
            }
        }

        $em->flush();
    }
}
