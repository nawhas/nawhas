<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents;

use App\Modules\Lyrics\Documents\Exceptions\UnsupportedFormatException;

class Factory
{
    /**
     * @throws \JsonException
     */
    public static function create(string $content, Format $format): Document
    {
        return match ($format) {
            Format::PlainText => new PlainText\Document($content),
            Format::JsonV1 => JsonV1\Document::fromJson($content),
        };
    }
}
