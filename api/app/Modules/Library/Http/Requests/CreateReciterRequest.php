<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CreateReciterRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('reciters', 'name'),
            ],
            'description' => ['string'],
        ];
    }

    public function name(): string
    {
        return $this->get('name');
    }

    public function description(): ?string
    {
        return $this->get('description');
    }
}
