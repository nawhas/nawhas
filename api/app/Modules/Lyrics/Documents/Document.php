<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @template TKey of array-key
 * @template TValue
 * @template-extends Arrayable<TKey, TValue>
 */
interface Document extends Arrayable
{
    public function getContent(): string;
    public function getFormat(): Format;
    public function render(): string;
    public function isEmpty(): bool;
    public function toArray(): array;
    public function __toString();
}
