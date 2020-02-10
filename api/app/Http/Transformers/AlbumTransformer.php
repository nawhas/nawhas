<?php declare(strict_types=1);

namespace App\Http\Transformers;

use App\Entities\Album;
use League\Fractal\Resource\Item;

class AlbumTransformer extends Transformer
{
    public function toArray(Album $album): array
    {
        return [
            'id' => $album->getId(),
            'reciterId' => $album->getReciter()->getId(),
            'title' => $album->getTitle(),
            'year' => $album->getYear(),
            $this->timestamps($album),
        ];
    }

    public function includeReciter(Album $album): Item
    {
        return $this->item($album->getReciter(), new ReciterTransformer());
    }
}
