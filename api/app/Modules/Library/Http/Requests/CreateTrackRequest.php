<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;
use App\Modules\Library\Http\Requests\Traits\HasAlbumRouteParameter;
use App\Modules\Library\Http\Requests\Traits\HasReciterRouteParameter;
use Illuminate\Validation\Rule;

class CreateTrackRequest extends Request
{
    use HasReciterRouteParameter;
    use HasAlbumRouteParameter;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                Rule::unique('tracks', 'title')
                    ->where('album_id', $this->album()->id)
            ],
            'lyrics' => [
                'string',
            ],
            'format' => [
                'integer',
            ],
            'video' => [
                'string',
                'nullable',
                'url'
            ],
            'topics' => [
                'array',
                'nullable'
            ]
        ];
    }
}
