<?php

namespace App\Console\Commands\Migrations;

use App\Database\Doctrine\EntityManager;
use App\Queries\MediaQuery;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateMediaUriToPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:media-uri';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace media URIs in the database with paths.';

    /**
     * Execute the console command.
     */
    public function handle(EntityManager $em): void
    {
        $media = MediaQuery::make()->all();

        foreach ($media as $audio) {
            $path = $audio->getPath();

            if (!Str::startsWith($path, 'https://')) {
                continue;
            }

            $path = str_replace('https://s3.us-east-2.amazonaws.com/staging.nawhas/', '', $path);

            $audio->setPath($path);
            $em->persist($audio);
        }

        $em->flush();
        $this->info('Done!');
    }
}
