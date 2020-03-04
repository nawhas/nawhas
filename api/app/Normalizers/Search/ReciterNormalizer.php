<?php

declare(strict_types=1);

namespace App\Normalizers\Search;

use App\Entities\Reciter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Zain\LaravelDoctrine\Algolia\Searchable;

class ReciterNormalizer implements NormalizerInterface
{
    /**
     * @param Reciter $object
     *
     * @return array<string,mixed>
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'avatar' => Storage::url($object->getAvatar()),
            'url' => "/reciters/{$object->getSlug()}",
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Reciter && $format === Searchable::NORMALIZATION_FORMAT;
    }
}
