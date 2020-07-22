<?php

declare(strict_types=1);

namespace App\Modules\Lyrics\Documents\JsonV1;

use App\Modules\Lyrics\Documents\Document as DocumentContract;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;

class Document implements DocumentContract, Jsonable
{
    private Metadata $meta;
    private Lyrics $data;

    public function __construct(Metadata $meta, Lyrics $data)
    {
        $this->meta = $meta;
        $this->data = $data;
    }

    public static function make(Metadata $meta = null, Lyrics $data = null): self
    {
        return new self($meta ?? new Metadata(), $data ?? new Lyrics());
    }

    public static function fromJson(string $content): self
    {
        $source = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $document = self::make(new Metadata(Arr::get($source, 'meta.timestamps', true)));

        foreach (Arr::get($source, 'data', []) as $data) {
            $group = new Group(
                Arr::get($data, 'timestamp', 0),
                Arr::get($data, 'type', Group::TYPE_NORMAL)
            );

            $document->lyrics()->addGroup($group);

            foreach (Arr::get($data, 'lines', []) as $line) {
                $group->addLine(new Line(
                    Arr::get($line, 'text', ''),
                    Arr::get($line, 'repeat', 0)
                ));
            }
        }

        return $document;
    }

    public function meta(): Metadata
    {
        return $this->meta;
    }

    public function lyrics(): Lyrics
    {
        return $this->data;
    }

    public function getFormat(): Format
    {
        return Format::JSON_V1();
    }

    public function toArray(): array
    {
        return [
            'meta' => $this->meta->toArray(),
            'data' => $this->data->toArray(),
        ];
    }

    public function render(): string
    {
        return $this->data->render();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function isEmpty(): bool
    {
        return (new Stringable($this->render()))
            ->trim()
            ->isEmpty();
    }
}
