<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents\JsonV1;

class Metadata
{
    private bool $timestamps;

    public function __construct(bool $timestamps = true)
    {
        $this->timestamps = $timestamps;
    }

    public function showTimestamps(): bool
    {
        return $this->timestamps;
    }

    public function timestamps(bool $timestamps): self
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
