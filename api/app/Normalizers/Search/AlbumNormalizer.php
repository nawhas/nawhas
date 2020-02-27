<?php

declare(strict_types=1);

namespace App\Normalizers\Search;

use App\Entities\Album;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Zain\LaravelDoctrine\Algolia\Searchable;

class AlbumNormalizer implements NormalizerInterface
{
    /**
     * @param Album $object
     *
     * @return array<string,mixed>
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'year' => $object->getYear(),
            'artwork' => $object->getArtwork(),
            'reciter' => [
                'name' => $object->getReciter()->getName(),
            ],
            'url' => sprintf('/reciters/%s/albums/%s', $object->getReciter()->getSlug(), $object->getYear()),
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Album && $format === Searchable::NORMALIZATION_FORMAT;
    }
}
