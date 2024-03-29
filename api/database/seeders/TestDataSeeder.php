<?php

namespace Database\Seeders;

use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Models\User;
use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use App\Modules\Lyrics\Documents\Factory;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Database\Seeder;

use function logger;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        if (User::count() > 0) {
            logger()->debug('Database already seeded. Exiting');
            return;
        }

        $data = json_decode(file_get_contents(__DIR__ . '/seed.json'), true);

        User::create(Role::Moderator, 'Moderator', 'moderator@nawhas.com', 'secret');
        User::create(Role::Contributor, 'Contributor', 'contributor@nawhas.com', 'secret');

        foreach ($data as $r) {
            $reciter = Reciter::create($r['name'], $r['description'], $r['avatar']);

            $albums = 0;
            foreach ($r['albums'] as $a) {
                if ($albums >= 10) {
                    break;
                }

                $album = Album::create($reciter, $a['title'], $a['year'], $a['artwork']);

                foreach ($a['tracks'] as $t) {
                    $track = Track::create($album, $t['title']);

                    if ($t['lyrics']) {
                        $doc = Factory::create($t['lyrics'], Format::PlainText);
                        $track->changeLyrics($doc);
                    }

                    if ($t['audio']) {
                        $track->changeAudio($t['audio']);
                    }
                }

                $albums++;
            }
        }
    }
}
