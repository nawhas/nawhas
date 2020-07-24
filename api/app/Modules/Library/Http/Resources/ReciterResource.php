<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Resources;

use App\Support\Resources\ResourceHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

class ReciterResource extends JsonResource
{
    use ResourceHelpers;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'avatar' => $this->avatar,
            $this->timestamps(),
        ];
    }
}
