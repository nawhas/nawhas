<?php

use App\Normalizers\Search as Normalizers;

return [
    'enabled' => env('ALGOLIA_ENABLED', env('ALGOLIA_APP_ID') !== null),
    'app' => env('ALGOLIA_APP_ID'),
    'secret' => env('ALGOLIA_API_KEY'),
    'settingsDirectory' => storage_path('algolia'),

    'search' => [
        // Number of results to retrieve on search (default: 20)
        'nbResults' => 20,

        // Use a prefix for index names
        'prefix' => env('ALGOLIA_PREFIX', null),

        // React to these Doctrine events automatically.
        // Set this to [] to disable automatic sync.
        'doctrineSubscribedEvents' => ['postPersist', 'postUpdate', 'preRemove'],

        'indices' => [
            'reciters' => [
                'class' => App\Entities\Reciter::class,
            ],
            'albums' => [
                'class' => App\Entities\Album::class,
            ],
            'tracks' => [
                'class' => App\Entities\Track::class,
            ],
        ],

        // For entities without a normalizer specified above, prepend this stack
        // to the default normalizer stack. Order matters.
        'normalizers' => [
            Normalizers\ReciterNormalizer::class,
            Normalizers\AlbumNormalizer::class,
            Normalizers\TrackNormalizer::class,
        ]
    ],
];
