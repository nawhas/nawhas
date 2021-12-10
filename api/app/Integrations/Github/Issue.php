<?php

declare(strict_types=1);

namespace App\Integrations\Github;

class Issue
{
    /**
     * @param array<string> $labels
     */
    public function __construct(
        private readonly string $title,
        private readonly string $body,
        private readonly array $labels = []
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return array|string[]
     */
    public function getLabels()
    {
        return $this->labels;
    }
}
