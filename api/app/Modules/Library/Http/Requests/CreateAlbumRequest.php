<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;
use App\Modules\Library\Http\Requests\Traits\HasReciterRouteParameter;
use Illuminate\Validation\Rule;

class CreateAlbumRequest extends Request
{
    use HasReciterRouteParameter;

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
            ],
            'year' => [
                'required',
                'string',
                Rule::unique('albums', 'year')
                    ->where('reciter_id', $this->reciter()->id)
            ],
        ];
    }
}
