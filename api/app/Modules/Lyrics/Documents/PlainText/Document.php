<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents\PlainText;

use App\Modules\Lyrics\Documents\Document as DocumentContract;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Support\Stringable;

/**
 * @implements DocumentContract<string,string|int>
 */
class Document implements DocumentContract
{
    public function __construct(
        private readonly string $content
    ) {}

    public function getFormat(): Format
    {
        return Format::PlainText;
    }

    public function render(): string
    {
        // Trim unnecessary whitespace
        $content = preg_replace("/\n{2,}/", "\n", $this->content);

        $content = trim($content);

        return $content;
    }

    public function isEmpty(): bool
    {
        return (new Stringable($this->render()))
            ->trim()
            ->isEmpty();
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function toArray(): array
    {
        return [
            'content' => $this->getContent(),
            'format' => $this->getFormat()->value,
        ];
    }
}
