<?php

$index = fn(string $name): string => env('SCOUT_PREFIX', '') . $name;

return [
    'host' => env('MEILISEARCH_HOST', 'http://nawhas_search:7700'),
    'key' => env('MEILISEARCH_KEY', 'secret'),

    'indices' => [
        $index('reciters') => [
            'model' => App\Modules\Library\Models\Reciter::class,
        ],
        $index('albums') => [
            'model' => App\Modules\Library\Models\Album::class,
        ],
        $index('tracks') => [
            'model' => App\Modules\Library\Models\Track::class,
        ],
    ],
];
