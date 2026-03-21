<?php

declare(strict_types=1);

namespace App\Modules\Stories\Http\Transformers;

use App\Modules\Core\Transformers\Transformer;
use App\Modules\Stories\Models\Story;

class StoryTransformer extends Transformer
{
    public function toArray(Story $story): array
    {
        return [
            'id' => $story->id,
            'slug' => $story->slug,
            'title' => $story->title,
            'excerpt' => $story->excerpt,
            'body' => $story->body,
            'heroImageUrl' => $story->hero_image_url,
            'displayDate' => $story->display_date?->format('Y-m-d'),
            'publishedAt' => $this->dateTime($story->published_at),
            'createdAt' => $this->dateTime($story->getCreatedAt()),
            'updatedAt' => $this->dateTime($story->getUpdatedAt()),
        ];
    }
}
