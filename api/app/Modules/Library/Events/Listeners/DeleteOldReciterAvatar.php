<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Listeners;

use App\Database\Doctrine\EntityManager;
use App\Modules\Library\Events\ReciterModified;
use Illuminate\Support\Facades\Storage;

class DeleteOldReciterAvatar
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ReciterModified $event): void
    {
        $reciter = $event->getEntity();

        $original = $this->em->getOriginalEntityData($reciter);

        if (!isset($original['avatar'])) {
            return;
        }

        if ($reciter->getAvatar() !== $original['avatar']) {
            Storage::delete($original['avatar']);
        }
    }
}
