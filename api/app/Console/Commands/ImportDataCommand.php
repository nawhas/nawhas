<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Database\Doctrine\EntityManager;
use App\Entities\Album;
use App\Entities\Lyrics;
use App\Entities\Media;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Repositories\AlbumRepository;
use App\Repositories\ReciterRepository;
use App\Repositories\TrackRepository;
use Aws\S3\S3Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportDataCommand extends Command
{
    protected $signature = 'data:import {path?} {--limit=}';
    protected $description = 'Import reciters, albums, and nawhas from a folder.';

    private Filesystem $source;
    private Cloud $destination;
    private ReciterRepository $reciters;
    private AlbumRepository $albums;
    private TrackRepository $tracks;
    private EntityManager $em;

    public function handle(
        ReciterRepository $reciters,
        AlbumRepository $albums,
        TrackRepository $tracks,
        EntityManager $em
    ): bool {
        $this->source = Storage::disk('import');
        $this->reciters = $reciters;
        $this->albums = $albums;
        $this->tracks = $tracks;
        $this->em = $em;

        /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
        $s3 = Storage::disk('s3');
        $this->destination = $s3;

        if (!$this->source->exists('reciters')) {
            $this->error("The directory 'reciters' does not exist.");

            return false;
        }

        $this->comment('Importing data from S3...');

        if ($this->option('limit')) {
            $this->comment('Limiting to ' . intval($this->option('limit')) . ' reciters');
        }

        $this->importReciters('reciters');

        $this->em->flush();
        $this->info('✅  Done!');

        return true;
    }

    private function importReciters(string $base): void
    {
        $directories = $this->source->directories($base);
        $limit = ($this->option('limit')) ? (int)$this->option('limit') : null;

        $count = count($directories);
        if (!$count > 0) {
            $this->error('There are no reciters');

            return;
        }

        $this->comment('Found ' . $count . ' reciters.');

        $progress = $this->createCustomProgressBar($count, 'Starting to import reciters...');

        $count = 0;
        foreach ($directories as $directory) {
            $this->importReciter($directory, $progress);
            $progress->advance();
            $count++;

            if ($limit && $count === $limit) {
                break;
            }
        }

        $progress->finish();
        $this->output->newLine(2);

        $this->info("{$count} reciters imported.");
    }

    private function importReciter(string $directory, ProgressBar $bar): void
    {
        $name = trim(basename($directory));
        $bar->setMessage("Importing \"$name\"");

        $reciter = $this->reciters->query()->whereName($name)->first();

        if ($reciter === null) {
            $reciter = new Reciter($name);
        }

        // Persist the reciter.
        $this->em->persist($reciter);

        $this->importAlbumsForReciter($reciter, $directory, $bar);

        $this->em->flush();
    }

    private function importAlbumsForReciter(Reciter $reciter, string $directory, ProgressBar $bar): void
    {
        $directories = $this->source->directories($directory);
        $bar->setMessage('Importing ' . count($directories) . ' albums for ' . $reciter->getName());

        foreach ($directories as $directory) {
            $albumText = trim(basename($directory));

            [$year, $title] = explode(' - ', $albumText);
            $bar->setMessage("Importing \"{$reciter->getName()}\"'s {$year} album called \"{$title}\".");

            $album = $this->albums->query()
                ->whereIdentifier($year)
                ->whereReciter($reciter)
                ->first();

            if ($album === null) {
                $album = new Album($reciter, $title, $year);
            }

            $this->em->persist($album);

            $this->importTracks($reciter, $album, $directory, $bar);
        }
    }

    private function importTracks(Reciter $reciter, Album $album, string $directory, ProgressBar $bar)
    {
        $directories = $this->source->directories($directory);
        $bar->setMessage('Importing ' . count($directories) . " tracks for {$reciter->getName()}'s {$album->getYear()} album.");
        $bar->display();

        foreach ($directories as $directory) {
            $base = trim(basename($directory));
            [, $title] = explode(' - ', $base);

            $track = $this->tracks->query()->whereAlbum($album)->whereTitle($title)->first();

            if ($track === null) {
                $track = new Track($album, $title);
            }

            // Audio File
            $audio = collect($this->source->files($directory))
                ->filter(fn (string $name) => Str::startsWith(basename($name), 'audio.'))
                ->first();

            if ($audio && $this->source->size($audio) > 0) {
                $client = $this->getS3Client();
                $ext = pathinfo($audio, PATHINFO_EXTENSION);
                $destination = "reciters/{$reciter->getSlug()}/albums/{$album->getYear()}/tracks/{$track->getSlug()}.{$ext}";

                $client->copy(
                    config('filesystems.disks.import.bucket'),
                    $audio,
                    config('filesystems.disks.s3.bucket'),
                    $destination,
                    'public-read'
                );

                $track->addAudioFile(Media::audioFile($this->destination->url($destination)));
            }

            // Lyrics
            if ($this->source->exists($directory . '/lyrics.txt')) {
                $text = $this->getLyricsFromFile($directory);
                $lyrics = new Lyrics($track, $text, Lyrics::FORMAT_PLAIN_TEXT);
                $track->replaceLyrics($lyrics);
            }

            $this->em->persist($track);
        }
    }

    private function createCustomProgressBar(int $max, string $message): ProgressBar
    {
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% ↠ %message%');

        $bar = $this->output->createProgressBar($max);
        $bar->setFormat('custom');
        $bar->setMessage($message);

        return $bar;
    }

    private function getLyricsFromFile(string $directory): string
    {
        $text = trim($this->source->get($directory . '/lyrics.txt'));
        $text = mb_scrub($text);
        return $text;
    }

    private function getS3Client(): S3Client
    {
        /** @var \League\Flysystem\Filesystem $driver */
        $driver = $this->source->getDriver();

        /** @var AwsS3Adapter $adapter */
        $adapter = $driver->getAdapter();

        /** @var S3Client $client */
        $client = $adapter->getClient();

        return $client;
    }
}
