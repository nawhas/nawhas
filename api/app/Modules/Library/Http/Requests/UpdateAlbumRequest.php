<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;
use App\Modules\Authentication\Enum\Role;
use App\Modules\Library\Http\Requests\Traits\HasAlbumRouteParameter;
use App\Modules\Library\Http\Requests\Traits\HasReciterRouteParameter;
use Illuminate\Validation\Rule;

class UpdateAlbumRequest extends Request
{
    use HasReciterRouteParameter;
    use HasAlbumRouteParameter;

    public function authorize(): bool
    {
        return $this->user()->role === Role::MODERATOR;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                Rule::unique('albums', 'year')
                    ->where('reciter_id', $this->reciter()->id)
                    ->ignoreModel($this->album())
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
