<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Database\Doctrine\EntityManager;
use App\Entities\Album;
use App\Entities\Lyrics;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Repositories\AlbumRepository;
use App\Repositories\ReciterRepository;
use App\Repositories\TrackRepository;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportDataCommand extends Command
{
    protected $signature = 'data:import {path?}';
    protected $description = 'Import reciters, albums, and nawhas from a folder.';

    private Filesystem $filesystem;
    private ReciterRepository $reciters;
    private AlbumRepository $albums;
    private TrackRepository $tracks;
    private EntityManager $em;

    public function __construct(
        Filesystem $filesystem,
        ReciterRepository $reciters,
        AlbumRepository $albums,
        TrackRepository $tracks,
        EntityManager $em
    ) {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->reciters = $reciters;
        $this->albums = $albums;
        $this->tracks = $tracks;
        $this->em = $em;
    }

    public function handle(): bool
    {
        $directory = $this->argument('path') ?? storage_path('data/reciters');

        if (!$this->filesystem->exists($directory)) {
            $this->error("The directory {$directory} does not exist.");

            return false;
        }

        $this->comment('Importing data from ' . $directory . '');

        $this->importReciters($directory);

        $this->em->flush();
        $this->info('✅  Done!');

        return true;
    }

    private function importReciters(string $base): void
    {
        $directories = $this->filesystem->directories($base);

        $count = count($directories);
        if (!$count > 0) {
            $this->error('There are no reciters');

            return;
        }

        $this->comment('Found ' . $count . ' reciters.');

        $progress = $this->createCustomProgressBar($count, 'Starting to import reciters...');

        foreach ($directories as $directory) {
            $this->importReciter($directory, $progress);
            $progress->advance();
        }

        $progress->finish();
        $this->output->newLine(2);

        $this->info("{$count} reciters imported.");
    }

    private function importReciter(string $directory, ProgressBar $bar): void
    {
        $name = trim($this->filesystem->basename($directory));
        $bar->setMessage("Importing \"$name\"");

        $reciter = $this->reciters->query()->whereName($name)->first();

        if ($reciter === null) {
            $reciter = new Reciter($name);
        }

        // Set the bio for the reciter if bio.txt exists.
        $bioFile = $directory . '/bio.txt';
        if ($this->filesystem->exists($bioFile)) {
            $reciter->setDescription(trim($this->filesystem->get($bioFile)));
        }

        // TODO - Add avatar handling

        // Persist the reciter.
        $this->em->persist($reciter);
        $this->em->flush();


        $this->importAlbumsForReciter($reciter, $directory, $bar);
    }

    private function importAlbumsForReciter(Reciter $reciter, string $directory, ProgressBar $bar): void
    {
        $directories = $this->filesystem->directories($directory);
        $bar->setMessage('Importing ' . count($directories) . ' albums for ' . $reciter->getName());

        foreach ($directories as $directory) {
            $albumText = trim($this->filesystem->basename($directory));

            [$year, $title] = explode(' - ', $albumText);
            $bar->setMessage("Importing \"{$reciter->getName()}\"'s {$year} album called \"{$title}\".");

            $album = $this->albums->query()
                ->whereIdentifier($year)
                ->whereReciter($reciter)
                ->first();

            if ($album === null) {
                $album = new Album($reciter, $title, $year);
            }

            // TODO - Import artwork.

            $this->em->persist($album);

            $this->importTracks($reciter, $album, $directory, $bar);
        }
    }

    private function importTracks(Reciter $reciter, Album $album, string $directory, ProgressBar $bar)
    {
        $directories = $this->filesystem->directories($directory);
        $bar->setMessage('Importing ' . count($directories) . " tracks for {$reciter->getName()}'s {$album->getYear()} album.");

        foreach ($directories as $directory) {
            $base = trim($this->filesystem->basename($directory));
            [, $title] = explode(' - ', $base);

            $track = $this->tracks->query()->whereAlbum($album)->whereTitle($title)->first();

            if ($track === null) {
                $track = new Track($album, $title);
            }

            // TODO - Handle audio file upload.

            // Lyrics
            if ($this->filesystem->exists($directory . '/lyrics.txt')) {
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
        $text = trim($this->filesystem->get($directory . '/lyrics.txt'));
        $text = mb_scrub($text);
        return $text;
    }
}
