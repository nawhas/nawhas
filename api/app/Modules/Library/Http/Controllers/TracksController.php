<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Validation\ValidationException;
use App\Modules\Library\Events\{Tracks\TrackDeleted, Tracks\TrackViewed};
use App\Modules\Library\Http\Transformers\TrackTransformer;
use App\Modules\Library\Models\{Album, Lyrics, Reciter, Track};
use App\Support\Pagination\PaginationState;
use Illuminate\Http\{JsonResponse, Request, Response};

class TracksController extends Controller
{
    public function __construct(TrackTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function index(Request $request, string $reciterId, string $albumId): JsonResponse
    {
        $album = Album::retrieve($albumId, Reciter::retrieve($reciterId)->id);

        $tracks = $album->tracks()
            ->orderBy('title')
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithPaginator($tracks);
    }

    public function store(Request $request, string $reciterId, string $albumId): JsonResponse
    {
        $album = Album::retrieve($albumId, Reciter::retrieve($reciterId)->id);

        $track = Track::create($album, $request->get('title'));

        if ($request->has('lyrics')) {
            Lyrics::create(
                $track,
                $request->get('content'),
                $request->get('format', Format::PLAIN_TEXT)
            );
        }

        return $this->respondWithItem($track->fresh());
    }

    public function show(string $reciterId, string $albumId, string $trackId): JsonResponse
    {
        $album = Album::retrieve($albumId, Reciter::retrieve($reciterId)->id);
        $track = Track::retrieve($trackId, $album->id);

        event(new TrackViewed($track->id));

        return $this->respondWithItem($track);
    }

    public function update(Request $request, string $reciterId, string $albumId, string $trackId): JsonResponse
    {
        $album = Album::retrieve($albumId, Reciter::retrieve($reciterId)->id);
        $track = Track::retrieve($trackId, $album->id);

        if ($request->has('title')) {
            $track->changeTitle($request->get('title'));
        }

        if ($request->has('lyrics')) {
            // todo
        }

        return $this->respondWithItem($track->fresh());
    }

    public function destroy(string $reciterId, string $albumId, string $trackId): Response
    {
        $album = Album::retrieve($albumId, Reciter::retrieve($reciterId)->id);
        $track = Track::retrieve($trackId, $album->id);

        event(new TrackDeleted($track->id));

        return response()->noContent();
    }

    public function uploadTrackMedia(Request $request, string $reciterId, string $albumId, string $trackId): JsonResponse
    {
        $reciter = Reciter::retrieve($reciterId);
        $album = Album::retrieve($albumId, $reciter->id);
        $track = Track::retrieve($trackId, $album->id);

        if (!$request->file('audio')) {
            throw ValidationException::withMessages(['audio' => 'An audio file is required.']);
        }

        $path = $request->file('audio')
            ->storePublicly(
                sprintf(
                    'reciters/%s/albums/%s/tracks/%s',
                    $reciter->slug,
                    $album->year,
                    $track->slug,
                )
            );

        $track->changeAudio($path);

        return $this->respondWithItem($track);
    }
}
