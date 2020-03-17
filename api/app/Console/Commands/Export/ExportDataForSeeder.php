<?php

namespace App\Console\Commands\Export;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Queries\AlbumQuery;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Doctrine\ORM\Query;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ExportDataForSeeder extends Command
{
    /** @var string */
    protected $signature = 'data:export';

    /** @var string */
    protected $description = 'Export data for seeder';

    public function handle(EntityManager $em): int
    {
        $slugs = [
            'tejani-brothers', 'mir-hasan-mir', 'nadeem-sarwar',
            'irfan-haider', 'farhan-ali-waris', 'hassan-sadiq'
        ];

        $export = [];
        foreach ($this->getReciters($em, $slugs) as $reciter) {
            $exported = [
                'name' => $reciter->getName(),
                'description' => $reciter->getDescription(),
                'avatar' => $reciter->getAvatar(),
            ];

            $albums = [];
            foreach ($this->getAlbums($reciter) as $album) {
                $albumExport = [
                    'title' => $album->getTitle(),
                    'year' => $album->getYear(),
                    'artwork' => $album->getArtwork(),
                ];

                $tracks = [];
                foreach ($album->getTracks() as $track) {
                    $tracks[] = [
                        'title' => $track->getTitle(),
                        'lyrics' => optional($track->getLyrics())->getContent(),
                        'audio' => optional($track->getAudioFile())->getPath(),
                    ];
                }

                $albumExport['tracks'] = $tracks;
                $albums[] = $albumExport;
            }

            $exported['albums'] = $albums;

            $export[] = $exported;
        }

        echo json_encode($export);
        return 0;
    }

    /**
     * @param string[] $slugs
     * @return Collection|Reciter[]
     */
    private function getReciters(EntityManager $em, array $slugs): Collection
    {
        $builder = $em->getRepository(Reciter::class)->createQueryBuilder('r');

        $results = $builder
            ->where($builder->expr()->in('r.slug', $slugs))
            ->getQuery()
            ->getResult();

        return collect($results);
    }

    /**
     * @return Collection|Album[]
     */
    private function getAlbums(Reciter $reciter): Collection
    {
        return AlbumQuery::make()->whereReciter($reciter)->all();
    }
}
