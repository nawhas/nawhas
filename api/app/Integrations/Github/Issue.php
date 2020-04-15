<?php

declare(strict_types=1);

namespace App\Integrations\Github;

class Issue
{
    private string $title;
    private string $body;

    /**
     * @var array|string[]
     */
    private array $labels;

    /**
     * Issue constructor.
     * @param string $title
     * @param string $body
     * @param array|string[] $labels
     */
    public function __construct(string $title, string $body, array $labels = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->labels = $labels;
    }

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
