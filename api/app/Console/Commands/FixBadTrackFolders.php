<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class FixBadTrackFolders extends Command
{

    protected $signature = 'data:fix';
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function handle(): bool
    {
        $base = storage_path('data/reciters');

        foreach ($this->filesystem->directories($base) as $reciter) {
            foreach ($this->filesystem->directories($reciter) as $album) {
                foreach ($this->filesystem->directories($album) as $track) {
                    if (Str::contains($track, '.txt')) {
                        $newName = str_replace(['}', '.txt'], '', $track);
                        $this->filesystem->moveDirectory($track, $newName);
                        $this->comment('Renamed: ' . basename($track) . ' to ' . basename($newName));
                    }
                }
            }
        }

        $this->info('Done');
        return true;
    }
}
