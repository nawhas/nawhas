<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents\JsonV1;

class Line
{
    public function __construct(
        private string $text,
        private int $repeat = 0
    ) {}

    public function repeat(int $repeat = 2): static
    {
        $this->repeat = $repeat;

        return $this;
    }

    public function getRepeat(): int
    {
        return $this->repeat;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function render(): string
    {
        $rendered = $this->text;

        if ($this->repeat !== 0) {
            $rendered .= " (x{$this->repeat})";
        }

        return $rendered;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'repeat' => $this->repeat,
        ];
    }
}
