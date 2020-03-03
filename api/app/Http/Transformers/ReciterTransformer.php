<?php

declare(strict_types=1);

namespace App\Http\Transformers;

use App\Entities\Reciter;
use App\Queries\AlbumQuery;
use League\Fractal\Resource\Primitive;

class ReciterTransformer extends Transformer
{
    protected $availableIncludes = ['related'];

    public function toArray(Reciter $reciter): array
    {
        return [
            'id' => $reciter->getId(),
            'name' => $reciter->getName(),
            'slug' => $reciter->getSlug(),
            'avatar' => $reciter->getAvatar(),
            'description' => $reciter->getDescription(),
            $this->timestamps($reciter),
        ];
    }

    public function includeRelated(Reciter $reciter): Primitive
    {
        return $this->primitive([
            'albums' => AlbumQuery::make()->whereReciter($reciter)->count(),
        ]);
    }
}
