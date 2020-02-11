<?php

declare(strict_types=1);

namespace App\Http\Transformers;

use App\Entities\Reciter;

class ReciterTransformer extends Transformer
{
    public function toArray(Reciter $reciter): array
    {
        return [
            'id' => $reciter->getId(),
            'name' => $reciter->getName(),
            'avatar' => $reciter->getAvatar(),
            'description' => $reciter->getDescription(),
            $this->timestamps($reciter),
        ];
    }
}
