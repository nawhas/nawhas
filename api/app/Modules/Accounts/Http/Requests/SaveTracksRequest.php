<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SaveTracksRequest extends Request
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => [
                'string',
                Rule::exists('tracks', 'id')
            ],
        ];
    }

    public function attributes()
    {
        return [
            'ids.*' => 'track ID',
        ];
    }
}
