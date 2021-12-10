<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Http\Requests;

use App\Http\Requests\Request;
use App\Modules\Audit\Enum\EntityType;
use Illuminate\Validation\Rule;

class RemoveSavedTracksRequest extends Request
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => [
                'string',
                Rule::exists('tracks', 'id'),
                Rule::exists('saveables', 'saveable_id')
                    ->where('saveable_type', EntityType::TRACK->value)
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'ids.*' => 'track ID',
        ];
    }
}
