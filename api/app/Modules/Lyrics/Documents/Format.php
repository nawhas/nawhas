<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents;

use MyCLabs\Enum\Enum;

/**
 * @method static Format PLAIN_TEXT()
 * @method static Format JSON_V1()
 * @psalm-immutable
 */
final class Format extends Enum
{
    /**
     * Format 1:
     * @see
     */
    public const PLAIN_TEXT = 1;

    /**
     * Version 2:
     * JSON document:
     * [
     *   {
     *     "timestamp": int,
     *     "lines": [
     *       { "text": string, "repeat": optional<int> }
     *     ]
     *   }
     * ]
     */
    public const JSON_V1 = 2;
}
