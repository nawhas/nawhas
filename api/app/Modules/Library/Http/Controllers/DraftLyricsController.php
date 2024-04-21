<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Events\Drafts\Lyrics\DraftLyricsDeleted;
use App\Modules\Library\Exceptions\DraftUnavailableException;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\CreateDraftLyricsRequest;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\ListDraftLyricsRequest;
use App\Modules\Library\Http\Requests\Drafts\Lyrics\UpdateDraftLyricsRequest;
use App\Modules\Library\Http\Transformers\DraftLyricsTransformer;
use App\Modules\Library\Models\DraftLyrics;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DraftLyricsController extends Controller
{
    public function __construct(DraftLyricsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function index(ListDraftLyricsRequest $request): JsonResponse
    {
        $draftLyrics = $request->track()->draftLyrics()->firstOrFail();
        return $this->respondWithItem($draftLyrics);
    }

    /**
     * @throws AuthorizationException
     */
    public function lock(DraftLyrics $draftLyrics): JsonResponse
    {
        try {
            $this->authorize('lock', $draftLyrics);
            /** @phpstan-ignore-next-line  */
        } catch ( DraftUnavailableException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 423);
        }

        Cache::put("draftLyricsForTrack:{$draftLyrics->track_id}", Auth::id(), 3600);

        return response()->json();
    }

    /**
     * @throws AuthorizationException
     */
    public function unlock(DraftLyrics $draftLyrics): JsonResponse {
        try {
            $this->authorize('unlock', $draftLyrics);
            /** @phpstan-ignore-next-line */
        } catch (DraftUnavailableException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 423);
        }

        Cache::forget("draftLyricsForTrack:{$draftLyrics->track_id}");

        return response()->json();
    }

    /**
     * @throws \JsonException
     * @throws AuthorizationException
     */
    public function store(CreateDraftLyricsRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', DraftLyrics::class);
            /** @phpstan-ignore-next-line */
        } catch (DraftUnavailableException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 423);
        }

        $draftLyrics = DraftLyrics::create($request->getTrackId(), $request->getDocument());
        return $this->respondWithItem($draftLyrics);
    }

    public function show(DraftLyrics $draftLyrics): JsonResponse
    {
        return $this->respondWithItem($draftLyrics);
    }

    /**
     * @throws \JsonException
     * @throws AuthorizationException
     */
    public function update(UpdateDraftLyricsRequest $request, DraftLyrics $draftLyrics): JsonResponse
    {
        try {
            $this->authorize('update', $draftLyrics);
            /** @phpstan-ignore-next-line */
        } catch (DraftUnavailableException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 423);
        }

        $draftLyrics->changeDraftLyrics($request->getDocument());
        $this->unlock($draftLyrics);
        return $this->respondWithItem($draftLyrics->fresh());
    }

    /**
     * @throws AuthorizationException
     */
    public function delete(DraftLyrics $draftLyrics): JsonResponse
    {
        $this->authorize('delete', $draftLyrics);

        event(new DraftLyricsDeleted($draftLyrics->id));
        $this->unlock($draftLyrics);
        return response()->json();
    }

    /**
     * @throws AuthorizationException
     */
    public function publish(DraftLyrics $draftLyrics): JsonResponse
    {
        $this->authorize('publish', $draftLyrics);

        $draftLyrics->publishLyrics($draftLyrics->document);
        $this->unlock($draftLyrics);
        return response()->json();
    }
}
