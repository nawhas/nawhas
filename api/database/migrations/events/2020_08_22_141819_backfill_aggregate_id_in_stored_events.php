<?php

use App\Modules\Core\Events\StoredEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BackfillAggregateIdInStoredEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $library = [
            // Reciter Events
            'reciter.created',
            'reciter.changed.name',
            'reciter.changed.avatar',
            'reciter.changed.description',
            'reciter.deleted',
            // Album Events
            'album.created',
            'album.changed.title',
            'album.changed.year',
            'album.changed.artwork',
            'album.deleted',
            // Track Events
            'track.created',
            'track.changed.title',
            'track.changed.audio',
            'track.changed.lyrics',
            'track.deleted',
        ];

        $albums = collect();
        $tracks = collect();
        StoredEvent::whereIn('event_class', $library)->each(function(StoredEvent $event) use ($albums, $tracks) {
            switch ($event->event_class) {
                case 'reciter.created':
                case 'reciter.changed.name':
                case 'reciter.changed.avatar':
                case 'reciter.changed.description':
                case 'reciter.deleted':
                    $event->aggregate_uuid = $event->event_properties['id'];
                    break;
                case 'album.created':
                    $event->aggregate_uuid = $event->event_properties['reciterId'];
                    $albums->put($event->event_properties['id'], $event->event_properties['reciterId']);
                    break;
                case 'album.changed.title':
                case 'album.changed.year':
                case 'album.changed.artwork':
                case 'album.deleted':
                    $event->aggregate_uuid = $albums->get($event->event_properties['id']);
                    break;
                case 'track.created':
                    $albumId = $event->event_properties['albumId'];
                    $reciterId = $albums->get($albumId);
                    $tracks->put($event->event_properties['id'], $reciterId);
                    $event->aggregate_uuid = $reciterId;
                    break;
                case 'track.changed.title':
                case 'track.changed.audio':
                case 'track.changed.lyrics':
                case 'track.deleted':
                    $event->aggregate_uuid = $tracks->get($event->event_properties['id']);
                    break;
            }

            $event->save();
        });
//        Schema::table('stored_events', function (Blueprint $table) {
//
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('stored_events', function (Blueprint $table) {

//        });
    }
}
