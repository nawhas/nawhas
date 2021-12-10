<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents\JsonV1;

class Metadata
{
    public function __construct(
        private bool $timestamps = true
    ) {}

    public function showTimestamps(): bool
    {
        return $this->timestamps;
    }

    public function timestamps(bool $timestamps): static
    {
        $this->timestamps = $timestamps;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'timestamps' => $this->timestamps,
        ];
    }
}
