<?php

use App\Modules\Library\Events as Library;
use App\Modules\Authentication\Events as Authentication;
use App\Modules\Accounts\Events as Accounts;

return [

    /*
     * These directories will be scanned for projectors and reactors. They
     * will be registered to Projectionist automatically.
     */
    'auto_discover_projectors_and_reactors' => [
        app()->path('Modules'),
    ],

    /*
     * Projectors are classes that build up projections. You can create them by performing
     * `php artisan event-sourcing:create-projector`. When not using auto-discovery,
     * Projectors can be registered in this array or a service provider.
     */
    'projectors' => [
        // App\Projectors\YourProjector::class
    ],

    /*
     * Reactors are classes that handle side-effects. You can create them by performing
     * `php artisan event-sourcing:create-reactor`. When not using auto-discovery
     * Reactors can be registered in this array or a service provider.
     */
    'reactors' => [
        // App\Reactors\YourReactor::class
    ],

    /*
     * A queue is used to guarantee that all events get passed to the projectors in
     * the right order. Here you can set of the name of the queue.
     */
    'queue' => 'events',

    /*
     * When a Projector or Reactor throws an exception the event Projectionist can catch it
     * so all other projectors and reactors can still do their work. The exception will
     * be passed to the `handleException` method on that Projector or Reactor.
     */
    'catch_exceptions' => env('EVENT_PROJECTOR_CATCH_EXCEPTIONS', false),

    /*
     * This class is responsible for storing events in the EloquentStoredEventRepository.
     * To add extra behaviour you can change this to a class of your own. It should
     * extend the \Spatie\EventSourcing\Models\EloquentStoredEvent model.
     */
    'stored_event_model' => App\Modules\Core\Events\StoredEvent::class,

    /*
     * This class is responsible for storing events. To add extra behaviour you
     * can change this to a class of your own. The only restriction is that
     * it should implement \Spatie\EventSourcing\EloquentStoredEventRepository.
     */
    'stored_event_repository' => Spatie\EventSourcing\StoredEvents\Repositories\EloquentStoredEventRepository::class,

    /*
     * This class is responsible for storing snapshots. To add extra behaviour you
     * can change this to a class of your own. The only restriction is that
     * it should implement \Spatie\EventSourcing\EloquentSnapshotRepository.
     */
    'snapshot_repository' => Spatie\EventSourcing\Snapshots\EloquentSnapshotRepository::class,

    /*
     * This class is responsible for handling stored events. To add extra behaviour you
     * can change this to a class of your own. The only restriction is that
     * it should implement \Spatie\EventSourcing\HandleDomainEventJob.
     */
    'stored_event_job' => Spatie\EventSourcing\StoredEvents\HandleStoredEventJob::class,

    /*
     * Similar to Relation::morphMap() you can define which alias responds to which
     * event class. This allows you to change the namespace or class names
     * of your events but still handle older events correctly.
     */
    'event_class_map' => [
        // Reciter Events
        'reciter.created' => Library\Reciters\ReciterCreated::class,
        'reciter.changed.name' => Library\Reciters\ReciterNameChanged::class,
        'reciter.changed.avatar' => Library\Reciters\ReciterAvatarChanged::class,
        'reciter.changed.description' => Library\Reciters\ReciterDescriptionChanged::class,
        'reciter.deleted' => Library\Reciters\ReciterDeleted::class,
        'reciter.viewed' => Library\Reciters\ReciterViewed::class,
        // Album Events
        'album.created' => Library\Albums\AlbumCreated::class,
        'album.changed.title' => Library\Albums\AlbumTitleChanged::class,
        'album.changed.year' => Library\Albums\AlbumYearChanged::class,
        'album.changed.artwork' => Library\Albums\AlbumArtworkChanged::class,
        'album.deleted' => Library\Albums\AlbumDeleted::class,
        'album.viewed' => Library\Albums\AlbumViewed::class,
        // Track Events
        'track.created' => Library\Tracks\TrackCreated::class,
        'track.changed.title' => Library\Tracks\TrackTitleChanged::class,
        'track.changed.audio' => Library\Tracks\TrackAudioChanged::class,
        'track.changed.video' => Library\Tracks\TrackVideoChanged::class,
        'track.changed.lyrics' => Library\Tracks\TrackLyricsChanged::class,
        'track.deleted' => Library\Tracks\TrackDeleted::class,
        'track.viewed' => Library\Tracks\TrackViewed::class,
        // User Events
        'user.registered' => Authentication\UserRegistered::class,
        'user.changed.name' => Authentication\UserNameChanged::class,
        'user.changed.email' => Authentication\UserEmailChanged::class,
        'user.changed.nickname' => Authentication\UserNicknameChanged::class,
        'user.changed.password' => Authentication\UserPasswordChanged::class,
        // Auth Events
        'auth.login' => Authentication\UserLoggedIn::class,
        'auth.logout' => Authentication\UserLoggedOut::class,
        'auth.password.reset.requested' => Authentication\PasswordResetRequested::class,
        // Account Events
        'accounts.saves.track.added' => Accounts\Saves\TrackSaved::class,
        'accounts.saves.track.removed' => Accounts\Saves\SavedTrackRemoved::class,
        // Draft Lyrics
        'drafts.lyrics.created' => Library\Drafts\Lyrics\DraftLyricsCreated::class,
        'drafts.lyrics.changed' => Library\Drafts\Lyrics\DraftLyricsChanged::class,
        'drafts.lyrics.published' => Library\Drafts\Lyrics\DraftLyricsPublished::class,
        'drafts.lyrics.deleted' => Library\Drafts\Lyrics\DraftLyricsDeleted::class,
    ],

    /*
     * This class is responsible for serializing events. By default an event will be serialized
     * and stored as json. You can customize the class name. A valid serializer
     * should implement Spatie\EventSourcing\EventSerializers\EventSerializer.
     */
    'event_serializer' => App\Modules\Core\Events\EventSerializer::class,

    'event_normalizers' => [
        Spatie\EventSourcing\Support\CarbonNormalizer::class,
        Spatie\EventSourcing\Support\ModelIdentifierNormalizer::class,
        Symfony\Component\Serializer\Normalizer\DateTimeNormalizer::class,
        Symfony\Component\Serializer\Normalizer\ArrayDenormalizer::class,
        Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer::class,
        Spatie\EventSourcing\Support\ObjectNormalizer::class,
    ],

    /*
     * When replaying events, potentially a lot of events will have to be retrieved.
     * In order to avoid memory problems events will be retrieved as chunks.
     * You can specify the chunk size here.
     */
    'replay_chunk_size' => 1000,

    /*
     * In production, you likely don't want the package to auto-discover the event handlers
     * on every request. The package can cache all registered event handlers.
     * More info: https://docs.spatie.be/laravel-event-sourcing/v1/advanced-usage/discovering-projectors-and-reactors
     *
     * Here you can specify where the cache should be stored.
     */
    'cache_path' => storage_path('app/event-sourcing'),
];
