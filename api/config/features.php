<?php

use App\Modules\Features\Definitions;

return [
    'overrides' => [
        'local' => [
            Definitions\PublicUserRegistration::NAME => true,
            Definitions\SocialAuthentication::NAME => true,
        ],
        'staging' => [
            Definitions\PublicUserRegistration::NAME => true,
            Definitions\SocialAuthentication::NAME => true,
        ],
    ],
];
