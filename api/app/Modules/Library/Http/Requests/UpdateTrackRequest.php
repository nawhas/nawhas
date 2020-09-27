<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;
use App\Modules\Library\Http\Requests\Traits\HasAlbumRouteParameter;
use App\Modules\Library\Http\Requests\Traits\HasTrackRouteParameter;
use Illuminate\Validation\Rule;

class UpdateTrackRequest extends Request
{
    use HasAlbumRouteParameter;
    use HasTrackRouteParameter;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'string',
                Rule::unique('tracks', 'title')
                    ->where('album_id', $this->album()->id)
                    ->ignoreModel($this->track())
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
                'size:11'
            ],
        ];
    }
}
