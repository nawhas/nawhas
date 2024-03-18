<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents;

enum Format: int
{
    /**
     * Format 1:
     */
    case PlainText = 1;

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
    case JsonV1 = 2;
}
