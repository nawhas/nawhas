<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents\PlainText;

use App\Modules\Lyrics\Documents\Document as DocumentContract;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Support\Stringable;

class Document implements DocumentContract
{
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getFormat(): Format
    {
        return Format::PLAIN_TEXT();
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

    public function __toString()
    {
        return $this->render();
    }
}
