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
use Symfony\Component\Console\Helper\ProgressBar;

class ImportDataCommand extends Command
{
    protected $signature = 'data:import {path?}';
    protected $description = 'Import reciters, albums, and nawhas from a folder.';

    private Filesystem $source;
    private Cloud $destination;
    private ReciterRepository $reciters;
    private AlbumRepository $albums;
    private TrackRepository $tracks;
    private EntityManager $em;

    public function __construct(
        ReciterRepository $reciters,
        AlbumRepository $albums,
        TrackRepository $tracks,
        EntityManager $em
    ) {
        parent::__construct();
        $this->source = Storage::disk('import');
        $this->destination = Storage::disk('s3');
        $this->reciters = $reciters;
        $this->albums = $albums;
        $this->tracks = $tracks;
        $this->em = $em;
    }

    public function handle(): bool
    {
        if (!$this->source->exists('reciters')) {
            $this->error("The directory 'reciters' does not exist.");

            return false;
        }

        $this->comment('Importing data from S3...');

        $this->importReciters('reciters');

        $this->em->flush();
        $this->info('✅  Done!');

        return true;
    }

    private function importReciters(string $base): void
    {
        $directories = $this->source->directories($base);

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

            if ($count === 12) {
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
                /** @var S3Client $client */
                $client = $this->source->getDriver()->getAdapter()->getClient();
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
                $lyrics = new Lyrics($track, $text);
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
}
