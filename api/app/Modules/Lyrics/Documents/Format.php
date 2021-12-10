<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents;

enum Format: int
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
