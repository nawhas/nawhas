<?php

function ms_index(string $name): string
{
    return env('SCOUT_PREFIX', '') . $name;
}

return [
    'host' => env('MEILISEARCH_HOST', 'nawhas_search:7700'),
    'key' => env('MEILISEARCH_KEY', 'secret'),

    'indices' => [
        ms_index('reciters') => [
            'model' => App\Modules\Library\Models\Reciter::class,
        ],
        ms_index('albums') => [
            'model' => App\Modules\Library\Models\Album::class,
        ],
        ms_index('tracks') => [
            'model' => App\Modules\Library\Models\Track::class,
        ],
    ],
];
