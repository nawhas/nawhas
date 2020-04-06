<?php

declare(strict_types=1);

namespace App\Values\Lyrics\Documents\JsonV1;

use Illuminate\Support\Collection;

class Group
{
    public const TYPE_NORMAL = 'normal';
    public const TYPE_SPACER = 'spacer';

    private ?float $timestamp;
    private Collection $lines;
    private string $type;

    public function __construct(?float $timestamp, string $type = self::TYPE_NORMAL)
    {
        $this->timestamp = $timestamp;
        $this->type = $type;
        $this->lines = collect();
    }

    public function addLine(Line $line): self
    {
        $this->lines->add($line);

        return $this;
    }

    public function getLines(): array
    {
        return $this->lines->toArray();
    }

    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp,
            'type' => $this->type,
            'lines' => $this->lines->map(fn (Line $l) => $l->toArray()),
        ];
    }
}
