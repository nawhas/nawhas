<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\User;
use App\Entities\SocialAccount;
use LaravelDoctrine\Fluent\{EntityMapping,Fluent};

class SocialAccountMapping extends EntityMapping
{
    public function mapFor()
    {
        return SocialAccount::class;
    }

    public function map(Fluent $map)
    {
        $map->uuidPrimaryKey();
        $map->belongsTo(User::class, 'user')->cascadePersist();
        $map->string('provider');
        $map->string('providerId');
        $map->unique(['user_id', 'provider']);
        $map->timestamps();
    }
}
