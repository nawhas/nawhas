<?php

namespace Tests\Factories;

use App\Modules\Library\Models\DraftLyrics;
use App\Modules\Library\Models\Track;
use App\Modules\Lyrics\Documents\Document;
use \App\Modules\Lyrics\Documents\Factory as DocumentFactory;
use App\Modules\Lyrics\Documents\Format;

class DraftLyricsFactory extends Factory
{
    /**
     * @throws \JsonException
     */
    protected function defaults(): array
    {
        $document = $this->generateDocument();
        return [
            'document' => $document
        ];
    }

    public function create(?Track $track = null, array $attributes = []): DraftLyrics
    {
        $track = $track ?? $this->getTrackFactory()->create();
        $values = $this->merge($attributes);

        return DraftLyrics::create($track->id, $values->get('document'));
    }

    /**
     * @throws \JsonException
     */
    public function generateDocument(?Format $format = null): Document
    {
        $faker = \Faker\Factory::create();
        if (is_null($format)) {
            $formats = [Format::PlainText, Format::JsonV1];
            $format = $formats[array_rand($formats)];
        }
        switch ($format) {
            case Format::PlainText:
                $text = $faker->paragraphs(3, true);
                break;
            case Format::JsonV1:
                $text = json_encode([
                    "meta" => [
                        "timestamps" => $faker->boolean
                    ],
                    "data" => array_map(function () use ($faker) {
                        return [
                            "timestamp" => $faker->optional()->randomFloat(2, 0, 20),
                            "lines" => array_map(function () use ($faker) {
                                return [
                                    "text" => $faker->sentence,
                                    "repeat" => $faker->numberBetween(0, 5)
                                ];
                            }, range(1, $faker->numberBetween(1, 4))),
                            "type" => $faker->randomElement(["normal", "spacer"])
                        ];
                    }, range(1, $faker->numberBetween(1, 5)))
                ], JSON_THROW_ON_ERROR);
                break;
            default:
                throw new \InvalidArgumentException("Invalid format provided");
        }
        return DocumentFactory::create($text, $format);
    }
}
