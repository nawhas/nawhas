<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;
use App\Modules\Library\Http\Requests\Traits\HasAlbumRouteParameter;
use App\Modules\Library\Http\Requests\Traits\HasReciterRouteParameter;
use Illuminate\Validation\Rule;

class UpdateAlbumRequest extends Request
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
                'string',
                Rule::unique('albums', 'year')
                    ->where('reciter_id', $this->reciter()->id)
                    ->ignoreModel($this->album())
            ],
            'year' => [
                'string',
                Rule::unique('albums', 'year')
                    ->where('reciter_id', $this->reciter()->id)
            ],
        ];
    }
}
