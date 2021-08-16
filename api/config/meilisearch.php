<?php

$index = fn(string $name): string => env('SCOUT_PREFIX', '') . $name;


return [
    /*
    |--------------------------------------------------------------------------
    | Meilisearch Configuration
    |--------------------------------------------------------------------------
    | See https://github.com/meilisearch/meilisearch-laravel-scout
    | Index Settings https://docs.meilisearch.com/references/settings.html#update-settings
    */
    'host' => env('MEILISEARCH_HOST', 'http://nawhas_search:7700'),
    'key' => env('MEILISEARCH_KEY', 'secret'),

    'indices' => [
        $index('reciters') => [
            'model' => App\Modules\Library\Models\Reciter::class,
            'settings' => [
                'searchableAttributes' => ['name', 'description']
            ],
        ],
        $index('albums') => [
            'model' => App\Modules\Library\Models\Album::class,
            'settings' => [
                'searchableAttributes' => ['title', 'year', 'reciter'],
                'rankingRules' => ['typo', 'words', 'proximity', 'attribute', 'wordsPosition', 'exactness', 'desc(year)']
            ],
        ],
        $index('tracks') => [
            'model' => App\Modules\Library\Models\Track::class,
            'settings' => [
                'searchableAttributes' => [
                    'title', 'lyrics', 'year', 'reciter', 'album',
                ],
                'rankingRules' => ['typo', 'words', 'proximity', 'attribute', 'wordsPosition', 'exactness', 'desc(year)']
            ],
        ],
    ],
];
