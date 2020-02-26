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
                'name' => $object->getReciter()->getName(),
            ],
            'album' => [
                'title' => $object->getAlbum()->getTitle(),
                'year' => $object->getAlbum()->getYear(),
                'artwork' => $object->getAlbum()->getArtwork(),
            ],
            'lyrics' => $this->normalizeLyrics($object->getLyrics()),
            'url' => sprintf(
                '/reciters/%s/albums/%s/tracks/%s',
                $object->getReciter()->getSlug(),
                $object->getAlbum()->getYear(),
                $object->getSlug()
            ),
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
