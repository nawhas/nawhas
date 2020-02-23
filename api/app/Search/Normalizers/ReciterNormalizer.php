<?php

declare(strict_types=1);

namespace App\Search\Normalizers;

use App\Entities\Reciter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Reciter;
    }
}
