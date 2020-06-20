<?php

use App\Modules\Features\Definitions;

return [
    'overrides' => [
        'local' => [
            Definitions\PublicUserRegistration::NAME => true,
        ],
    ],
];
