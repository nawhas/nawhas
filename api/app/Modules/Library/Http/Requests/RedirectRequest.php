<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;
use App\Modules\Library\Http\Requests\Traits\HasReciterRouteParameter;
use Illuminate\Validation\Rule;

class RedirectRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reciter' => [
                'string',
                'required',
            ],
            'album' => [
                'string',
            ],
            'track' => [
                'string',
            ],
        ];
    }
}
