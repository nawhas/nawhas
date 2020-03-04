<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\{Album, Reciter, Media};
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * @method Media get()
 * @method Media|null first()
 * @method Collection|Media[] all()
 */
class MediaQuery extends Query
{
    protected static function entity(): string
    {
        return Media::class;
    }
}
