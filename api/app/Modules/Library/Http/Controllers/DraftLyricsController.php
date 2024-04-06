<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\CreateDraftLyricsRequest;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\GetDraftLyricsRequest;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\UpdateDraftLyricsRequest;
use App\Modules\Library\Http\Transformers\DraftLyricsTransformer;
use App\Modules\Library\Models\DraftLyrics;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DraftLyricsController extends Controller
{
    public function __construct(DraftLyricsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function index(GetDraftLyricsRequest $request): JsonResponse
    {
        $draftLyrics = $request->track()->draftLyrics()->firstOrFail();
        return $this->respondWithItem($draftLyrics);
    }

    /**
     * @throws \JsonException
     */
    public function store(CreateDraftLyricsRequest $request): JsonResponse
    {
        $draftLyrics = DraftLyrics::create($request->getTrackId(), $request->getDocument());
        return $this->respondWithItem($draftLyrics);
    }

    public function show(DraftLyrics $draftLyrics): JsonResponse
    {
        return $this->respondWithItem($draftLyrics);
    }

    /**
     * @throws \JsonException
     */
    public function update(UpdateDraftLyricsRequest $request, DraftLyrics $draftLyrics): JsonResponse
    {
        $draftLyrics->changeDraftLyrics($request->getDocument());
        return $this->respondWithItem($draftLyrics->fresh());
    }

    /**
     * @throws AuthorizationException
     */
    public function delete(DraftLyrics $draftLyrics): Response
    {
        $this->authorize('delete', $draftLyrics);

        $draftLyrics->deleteDraftLyrics();

        return response()->noContent();
    }
}
