<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CreateTopicRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('topics', 'name'),
            ],
            'description' => [
                'string',
            ],
        ];
    }
}
