<?php

namespace App\Console\Commands;

use App\Entities\Album;
use App\Entities\Reciter;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportAlbumsFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:csv:albums';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge data from the Albums CSV to the directory structure.';

    private Filesystem $filesystem;

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        // Read CSV File
        $csvData = $this->readCSV(storage_path('data/albums.csv'));

        $reciter = null;

        $base = storage_path('data/reciters');

        foreach ($csvData as $album)
        {
            $reciter = trim($album['Reciter Name']) ?: $reciter;
            $title = Str::title(trim($album['Album Name']));
            $year = trim($album['Album Year']);

            $this->comment("Importing {$reciter}'s $year album called '$title'");

            $reciterDirectory = "$base/$reciter";
            if (!$this->filesystem->exists($reciterDirectory)) {
                $this->error("No existing directory found for {$reciter}.");
                if (!$this->confirm('Should we create one now?')) {
                    $this->error('Aborting!');
                    return false;
                }

                $this->filesystem->makeDirectory($reciterDirectory);
            }

            // Find existing albums
            $existingAlbums = collect($this->filesystem->directories($reciterDirectory))->mapWithKeys(function ($dir) {
                 [$year, $title] = explode(' - ', basename($dir));

                 return [$year => $title];
            });

            // Handle collisions
            if (($existingAlbumTitle = $existingAlbums->get($year)) !== null) {
                if ($existingAlbumTitle !== $title) {
                    $this->error("Existing album for {$year} found with a different name.");

                    $choice = $this->choice('Choose which one to keep: ', [
                        $title,
                        $existingAlbumTitle
                    ]);

                    if ($choice === $title) {
                        $this->info("> Renaming directory: \n   from: $reciterDirectory/$year - $existingAlbumTitle \n     to: $reciterDirectory/$year - $title");
                        $this->filesystem->moveDirectory(
                            "$reciterDirectory/$year - $existingAlbumTitle",
                            "$reciterDirectory/$year - $title",
                        );
                    }
                    continue;
                } else {
                    continue;
                }
            }

            $this->info("> Creating directory: $reciterDirectory/$year - $title");
            $this->filesystem->makeDirectory("$reciterDirectory/$year - $title");
        }

        return true;
    }

    public function readCSV($file) {
        $header = null;
        $data = array();
        if (($handle = fopen($file, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, ',')) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);

            return $data;
        }
    }
}
