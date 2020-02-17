<?php

namespace App\Console\Commands;

use App\Console\Helpers\WriteUpCleaner;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CleanUpLyrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup Lyrics';

    private Filesystem $fs;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $fs)
    {
        parent::__construct();
        $this->fs = $fs;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): bool
    {
        $base = storage_path('data/reciters');

        $files = $this->fs->glob("$base/*/*/*/lyrics.txt");

        $this->output->progressStart(count($files));

        $cleaner = new WriteUpCleaner();

        foreach ($files as $lyrics) {
            $content = $this->fs->get($lyrics);
            $clean = $cleaner->cleanup($content);
            $this->fs->put($lyrics, $clean);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('Done!');

        return true;
    }
}
