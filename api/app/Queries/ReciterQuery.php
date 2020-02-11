<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\Reciter;
use Doctrine\Common\Collections\Collection;

/**
 * @method Reciter get()
 * @method Reciter|null first()
 * @method Collection|Reciter[] all()
 */
class ReciterQuery extends Query
{
    protected static function entity(): string
    {
        return Reciter::class;
    }
}
