<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\CreateDraftLyricsRequest;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\DeleteDraftLyricsRequest;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\GetDraftLyricsRequest;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\UpdateDraftLyricsRequest;

class DraftLyricsController extends Controller
{
    public function getDraftLyrics(GetDraftLyricsRequest $request)
    {

    }

    public function store(CreateDraftLyricsRequest $request)
    {

    }

    public function update(UpdateDraftLyricsRequest $request)
    {

    }

    public function destroy(DeleteDraftLyricsRequest $request)
    {

    }
}
