<?php

namespace App\Console\Commands\Events;

use App\Modules\Lyrics\Documents\Factory;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\Projectionist;
use App\Modules\Authentication\Events as Auth;
use App\Modules\Library\Events as Library;

class BaselineState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:initialize {--reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize baseline events from doctrine DB.';

    private Projectionist $projectionist;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Projectionist $projectionist)
    {
        parent::__construct();
        $this->projectionist = $projectionist;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('reset')) {
            $this->warn('Resetting events table...');
            $this->call('migrate:refresh', [
                '--database' => 'events',
                '--path' => 'database/migrations/events',
            ]);
            return 0;
        }

        $this->projectionist->withoutEventHandlers();

        $this->comment('>>> Initializing events...');

        $this->processTable('users', function ($user) {
            event(new Auth\UserRegistered($user->id, [
                'name' => $user->name,
                'role' => $user->role,
                'nickname' => $user->nickname,
                'email' => $user->email,
                'password' => $user->password,
                'rememberToken' => $user->remember_token,
            ]));
        });

        $this->processTable('reciters', function ($reciter) {
            event(new Library\Reciters\ReciterCreated($reciter->id, [
                'name' => $reciter->name,
                'description' => $reciter->description,
                'created_at' => $reciter->created_at,
            ]));

            if ($reciter->avatar) {
                event(new Library\Reciters\ReciterAvatarChanged($reciter->id, $reciter->avatar));
            }
        });

        $this->processTable('albums', function ($album) {
            event(new Library\Albums\AlbumCreated($album->id, $album->reciter_id, [
                'title' => $album->title,
                'year' => $album->year,
            ]));

            if ($album->artwork) {
                event(new Library\Albums\AlbumArtworkChanged($album->id, $album->artwork));
            }
        });

        $this->processTable('tracks', function ($track) {
            event(new Library\Tracks\TrackCreated($track->id, $track->album_id, [
                'title' => $track->title,
            ]));
        });

        $this->processTable('lyrics', function ($lyrics) {
            $document = Factory::create($lyrics->content, new Format($lyrics->format));
            event(new Library\Tracks\TrackLyricsChanged($lyrics->track_id, $document));
        });

        // Process Track Audio
        $builder = DB::connection('pgsql')
            ->table('track_media', 'tm')
            ->leftJoin('media', 'media_id', '=', 'id')
            ->orderBy('media.created_at');

        $this->processTableWithQuery($builder, 'media', function ($media) {
            event(new Library\Tracks\TrackAudioChanged($media->track_id, $media->path));
        });

        $this->info('>>> ✅  All events initialized.');
        return 0;
    }

    private function processTable(string $table, callable $callback): void
    {
        $connection = DB::connection('pgsql');
        $this->processTableWithQuery(
            $connection->table($table)->orderBy('created_at'),
            $table,
            $callback
        );
    }

    private function processTableWithQuery(Builder $builder, string $table, callable $callback): void
    {
        $this->warn(">> Processing {$table}...");

        $count = $builder->count();
        $bar = $this->output->createProgressBar($count);
        $builder->each(function ($row) use ($callback, $bar) {
            $callback($row);
            $bar->advance();
        });
        $bar->finish();
        $this->line('');
        $this->info("> {$count} {$table} processed.");
    }
}
