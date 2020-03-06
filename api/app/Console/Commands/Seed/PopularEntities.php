<?php

namespace App\Console\Commands\Seed;

use App\Database\Doctrine\EntityManager;
use App\Entities\Reciter;
use App\Queries\ReciterQuery;
use App\Repositories\ReciterRepository;
use App\Visits\Entities\ReciterVisit;
use App\Visits\Manager;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PopularEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:popular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed popular entities';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(EntityManager $em, ReciterRepository $repo)
    {
        $this->seedPopularReciters($em, $repo);
    }

    protected function seedPopularReciters(EntityManager $em, ReciterRepository $repo): void
    {
        $popular = $repo->popular()->map(fn (Reciter $reciter) => [$reciter->getName()])->toArray();
        $this->table(['Starting Point'], $popular);

        $slugs = [
            'nadeem-sarwar', 'irfan-haider', 'tejani-brothers',
            'farhan-ali-waris', 'hassan-sadiq', 'mir-hasan-mir'
        ];

        foreach ($slugs as $index => $slug) {
            $reciter = ReciterQuery::make()->whereIdentifier($slug)->get();
            $max = ((100 - $index) / 100) * 200;
            for ($i = 0; $i < $max; $i++) {
                $em->persist(new ReciterVisit($reciter, Carbon::now()->subDays(rand(0, 30))));
            }
        }

        $em->flush();

        $this->info('Done!');

        $popular = $repo->popular()->map(fn (Reciter $reciter) => [$reciter->getName()])->toArray();
        $this->table(['Starting Point'], $popular);
    }
}
