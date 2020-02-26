<?php

declare(strict_types=1);

namespace App\Normalizers\Search;

use App\Entities\Lyrics;
use App\Entities\Track;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Zain\LaravelDoctrine\Algolia\Searchable;

class TrackNormalizer implements NormalizerInterface
{
    /**
     * @param Track $object
     *
     * @return array<string,mixed>
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'slug' => $object->getSlug(),
            'reciter' => [
                'id' => $object->getReciter()->getId(),
                'name' => $object->getReciter()->getName(),
            ],
            'album' => [
                'id' => $object->getAlbum()->getId(),
                'title' => $object->getAlbum()->getTitle(),
            ],
            'lyrics' => $this->normalizeLyrics($object->getLyrics()),
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Track && $format === Searchable::NORMALIZATION_FORMAT;
    }

    private function normalizeLyrics(?Lyrics $lyrics): ?string
    {
        if (!$lyrics) {
            return null;
        }

        $content = $lyrics->getContent();

        // Trim unnecessary whitespace
        $content = preg_replace("/\n{2,}/", "\n", $content);

        $content = trim($content);

        return substr($content, 0, 8000);
    }
}
