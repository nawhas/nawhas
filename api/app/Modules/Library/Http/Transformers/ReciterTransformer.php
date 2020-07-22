<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Http\Transformers\Transformer;
use App\Modules\Library\Models\Reciter;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Resource\Primitive;

class ReciterTransformer extends Transformer
{
    protected $availableIncludes = ['related'];

    public function toArray(Reciter $reciter): array
    {
        return [
            'id' => $reciter->id,
            'name' => $reciter->name,
            'slug' => $reciter->slug,
            'avatar' => $reciter->avatar ? Storage::url($reciter->avatar) : null,
            'description' => $reciter->description,
            $this->timestamps($reciter),
        ];
    }

    public function includeRelated(Reciter $reciter): Primitive
    {
        return $this->primitive([
            'albums' => 0, // TODO - when album model is done.
        ]);
    }
}
