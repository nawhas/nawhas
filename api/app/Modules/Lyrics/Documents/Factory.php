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
        return match ($format->getValue()) {
            Format::PLAIN_TEXT => new PlainText\Document($content),
            Format::JSON_V1 => JsonV1\Document::fromJson($content),
            default => throw new UnsupportedFormatException(),
        };
    }
}
