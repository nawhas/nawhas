<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests;

use App\Http\Requests\Request;
use App\Modules\Library\Http\Requests\Traits\HasReciterRouteParameter;
use Illuminate\Validation\Rule;

class UpdateReciterRequest extends Request
{
    use HasReciterRouteParameter;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'string',
                Rule::unique('reciters', 'name')
                    ->ignoreModel($this->reciter()),
            ],
            'description' => [
                'string',
            ],
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
