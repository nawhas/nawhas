<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents;

interface Document
{
    public function getFormat(): Format;
    public function render(): string;
    public function __toString();
}
