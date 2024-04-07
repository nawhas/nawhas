<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests\Drafts\Lyrics;

use App\Http\Requests\Request;
use App\Modules\Library\Models\Track;
use Illuminate\Validation\Rule;

class ListDraftLyricsRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'track_id' => [
                'required',
                Rule::exists(Track::class, 'id')
            ],
        ];
    }

    public function track(): Track
    {
        return Track::retrieve($this->get('track_id'));
    }
}
