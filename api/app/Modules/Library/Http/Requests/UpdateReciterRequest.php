<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;

class UpdateReciterRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
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
