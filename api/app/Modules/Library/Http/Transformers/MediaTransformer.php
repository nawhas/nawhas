<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Transformers;

use App\Enum\MediaProvider;
use App\Enum\MediaType;
use App\Modules\Core\Transformers\Transformer;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;

class MediaTransformer extends Transformer
{
    #[ArrayShape([
        'id' => "string",
        'uri' => "string",
        'type' => "string",
        'provider' => "string",
        'createdAt' => "null|string",
        'updatedAt' => "null|string"
    ])]
    public function toArray($media): array
    {
        return [
            'id' => '(deprecated)',
            'uri' => Storage::url($media),
            'type' => MediaType::AUDIO->value,
            'provider' => MediaProvider::FILE->value,
            'createdAt' => $this->dateTime(now()),
            'updatedAt' => $this->dateTime(now()),
        ];
    }
}
