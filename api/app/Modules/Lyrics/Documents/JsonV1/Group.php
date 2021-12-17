<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents\JsonV1;

use Illuminate\Support\Collection;

class Group
{
    public const TYPE_NORMAL = 'normal';
    public const TYPE_SPACER = 'spacer';

    /** @var \Illuminate\Support\Collection<int, Line> */
    private Collection $lines;

    public function __construct(
        private ?float $timestamp,
        private string $type = self::TYPE_NORMAL,
    ) {
        $this->lines = collect();
    }

    public function addLine(Line $line): static
    {
        $this->lines->add($line);

        return $this;
    }

    public function getLines(): array
    {
        return $this->lines->toArray();
    }

    public function render(): string
    {
        return $this->lines->map(fn (Line $line) => $line->render())->join("\n");
    }

    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp,
            'type' => $this->type,
            'lines' => $this->lines->map(fn (Line $line) => $line->toArray()),
        ];
    }
}
