<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Modules\Core\Transformers\Transformer;
use App\Modules\Library\Models\Topic;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Resource\Primitive;

class TopicTransformer extends Transformer
{
    protected $availableIncludes = ['related'];

    public function toArray(Topic $topic): array
    {
        return [
            'id' => $topic->id,
            'name' => $topic->name,
            'description' => $topic->description,
            'slug' => $topic->slug,
            'image' => $topic->image ? Storage::url($topic->image) : null,
            $this->timestamps($topic),
        ];
    }

    public function includeRelated(Topic $topic): Primitive
    {
        return $this->primitive([
            'tracks' => $topic->tracks()->count(),
        ]);
    }
}
