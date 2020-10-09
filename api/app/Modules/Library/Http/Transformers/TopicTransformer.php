<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Modules\Core\Transformers\Transformer;
use App\Modules\Library\Models\Topic;

class TopicTransformer extends Transformer
{
    public function toArray(Topic $topic): array
    {
        return [
            'id' => $topic->id,
            'name' => $topic->name,
            'description' => $topic->description,
            'slug' => $topic->slug,
            $this->timestamps($topic),
        ];
    }
}
