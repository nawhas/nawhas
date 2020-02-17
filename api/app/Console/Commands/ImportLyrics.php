<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Helpers\TrackNameIdentifier;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ImportLyrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:merge:lyrics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge data from the lyrics directory structure to the reciters directory structure';

    private Filesystem $filesystem;
    private int $errors = 0;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): bool
    {
        $base = storage_path('data/lyrics');
        $recitersBase = storage_path('data/reciters');

        foreach ($this->filesystem->directories($base) as $directory) {
            $reciter = $this->filesystem->basename($directory);

            $this->comment("Importing lyrics for '$reciter'");

            $reciterDirectory = "$recitersBase/$reciter";
            if (!$this->filesystem->exists($reciterDirectory)) {
                $this->error(" > No directory found for '$reciter'");
                return false;
            }

            foreach ($this->filesystem->directories($directory) as $albumDirectory) {
                $year = $this->filesystem->basename($albumDirectory);

                $reciterAlbumYearDirectories = collect($this->filesystem->directories($reciterDirectory))
                    ->filter(fn (string $dir) => explode(' - ', basename($dir))[0] === $year);

                if ($reciterAlbumYearDirectories->count() === 0) {
                    $this->error(" > No album found in '$reciter' for '$year'");
                    return false;
                }

                if ($reciterAlbumYearDirectories->count() > 1) {
                    $this->error(" > Multiple albums found in '$reciter' for '$year'");
                    return false;
                }

                $this->line(" > Importing {$year} album lyrics.");
                $album = $reciterAlbumYearDirectories->first();
                $this->importAlbum($albumDirectory, $album);
            }
        }

        $this->error("Encountered {$this->errors} errors.");

        return true;
    }

    private function importAlbum(string $source, string $destination): void
    {
        $trackFiles = $this->filesystem->glob("$source/*.txt");
        $destTracks = collect($this->filesystem->directories($destination))
            ->mapWithKeys(function ($dir) {
                [$num, $name] = explode(' - ', basename($dir));
                return [(string)$num => $name];
            });

        foreach ($trackFiles as $trackFile) {
            $number = str_replace('.txt', '', basename($trackFile));
            $contents = $this->filesystem->get($trackFile);
            $this->info("   >> Importing Track #$number");
            try {
                $name = $this->getTrackNameFromContents($contents);
            } catch (\LogicException $e) {
                $this->warn("      >> Failed to determine title");
                $name = null;
                while (!$name) {
                    $name = trim(
                        $this->ask(
                            "What should we call this track? Here's a preview of the lyrics: \n"
                            . substr($contents, 0, 100)
                        )
                    );
                }
            }

            $existing = $destTracks->mapWithKeys(fn ($n, $k) => [$n => levenshtein($name, $n)])
                    ->sort()->keys();

            $this->comment(
                "      >> No exact match found. \n"
              . "      >> Original: '$name'\n"
            );
            $choices = [
                ...$existing->toArray(),
                'n' => "Create a new track",
            ];
            $choice = $this->choice('Choose a track to merge into.', $choices);

            switch ($choice) {
                case 'n':
                    $newTrackNumber = intval($number) * 100;
                    $newTrackDir = "{$destination}/{$newTrackNumber} - {$name}";

                    $this->comment("      >> Importing as new track called '$name'");
                    if (!$this->filesystem->isDirectory($newTrackDir)) {
                        $this->filesystem->makeDirectory($newTrackDir);
                    }
                     $this->filesystem->move($trackFile, "$newTrackDir/lyrics.txt");
                    break;
                default:
                    $index = (int)$choice;
                    $destTrackName = $choices[$index];
                    $destTrackNum = $destTracks->flip()->get($destTrackName);

                    $choice = $this->choice('⚠️  Choose an option', [
                        'm' => "Merge lyrics into '$destTrackName'",
                        'r' => "Rename '$destTrackName' to '$name'",
                    ], 'm');

                    switch ($choice) {
                        case 'm':
                            $this->comment("      >> Importing as {$destTrackName}.");
                            $trackDir = "$destination/{$destTrackNum} - $destTrackName";
                            $this->filesystem->move($trackFile, "$trackDir/lyrics.txt");
                            break;
                        case 'r':
                            $this->comment("      >> Renaming '{$destTrackName}' to '$name'.");
                            $number = $destTracks->flip()->get($destTrackName);
                            $trackDir = "$destination/{$number} - $destTrackName";
                            $newDir = "$destination/{$number} - $name";
                            $this->filesystem->moveDirectory($trackDir, $newDir);
                            $this->filesystem->move($trackFile, "$newDir/lyrics.txt");
                            break;
                    }
            }

            // If there are no track directories yet, easy peasy.
//            if ($destTracks->isEmpty()) {
//                $trackDir = "$destination/{$number} - $name}";
//                if (!$this->filesystem->isDirectory($trackDir)) {
//                    $this->filesystem->makeDirectory($trackDir);
//                }
//                $this->filesystem->move($trackFile, "$trackDir/lyrics.txt");
//            } else {
//                $closest = $destTracks->mapWithKeys(fn ($n, $k) => [$k => levenshtein($name, $n)])
//                    ->sort()->keys()->first();
//
//                $closestMatchingDestTrackName = $destTracks->get($closest);
//
//                $this->comment(
//                    "      >> No exact match found. \n"
//                  . "      >> Original: '$name'\n"
//                  . "      >> Matching: '$closestMatchingDestTrackName'"
//                );
//
//                $choice = $this->choice('⚠️  Choose an option', [
//                    'm' => "Import lyrics into '$closestMatchingDestTrackName'",
//                    'r' => "Rename '$closestMatchingDestTrackName' to '$name'",
//                    's' => 'Skip for now'
//                ], 's');
//
//                switch ($choice) {
//                    case 's':
//                        $this->warn('      >> Skipping for now.');
//                        continue 2;
//                    case 'm':
//                        $this->comment("      >> Importing as {$closestMatchingDestTrackName}.");
//                        $trackDir = "$destination/{$destTracks->flip()->get($closestMatchingDestTrackName)} - $closestMatchingDestTrackName";
//                        $this->filesystem->move($trackFile, "$trackDir/lyrics.txt");
//                        break;
//                    case 'r':
//                        $this->comment("      >> Renaming '{$closestMatchingDestTrackName}' to '$name'.");
//                        $number = $destTracks->flip()->get($closestMatchingDestTrackName);
//                        $trackDir = "$destination/{$number} - $closestMatchingDestTrackName";
//                        $newDir = "$destination/{$number} - $name";
//                        $this->filesystem->moveDirectory($trackDir, $newDir);
//                        $this->filesystem->move($trackFile, "$newDir/lyrics.txt");
//                        break;
//                }
//            }
        }
    }

    private function getTrackNameFromContents(string $track): string
    {
        return (new TrackNameIdentifier())->getTrackNameFromContents($track);
    }
}
