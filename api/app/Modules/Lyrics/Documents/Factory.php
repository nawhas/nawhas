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
        switch ($format->getValue()) {
            case Format::PLAIN_TEXT:
                return new PlainText\Document($content);
            case Format::JSON_V1:
                return JsonV1\Document::fromJson($content);
            default:
                throw new UnsupportedFormatException();
        }
    }
}
