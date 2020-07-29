<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Projectors;

use App\Modules\Authentication\Events\SocialAccountRegistered;
use App\Modules\Authentication\Models\SocialAccount;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class SocialAccountsProjector extends Projector
{
    public function onSocialAccountRegistered(SocialAccountRegistered $event): void
    {
        $data = collect($event->attributes);
        $data->put('id', $event->id);

        $socialAccount = new SocialAccount($data->all());

        $socialAccount->saveOrFail();
    }

    public function resetState(): void
    {
        SocialAccount::truncate();
    }
}
