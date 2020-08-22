<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Http\Requests\CreateTrackRequest;
use App\Modules\Library\Http\Requests\UpdateTrackRequest;
use App\Modules\Lyrics\Documents\Factory as DocumentFactory;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Validation\ValidationException;
use App\Modules\Library\Events\{Tracks\TrackDeleted, Tracks\TrackViewed};
use App\Modules\Library\Http\Transformers\TrackTransformer;
use App\Modules\Library\Models\{Album, Reciter, Track};
use App\Support\Pagination\PaginationState;
use Illuminate\Http\{JsonResponse, Request, Response};

class TracksController extends Controller
{
    public function __construct(TrackTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function index(Request $request, Reciter $reciter, Album $album): JsonResponse
    {
        $tracks = $album->tracks()
            ->orderBy('title')
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithPaginator($tracks);
    }

    public function store(CreateTrackRequest $request, Reciter $reciter, Album $album): JsonResponse
    {
        $track = Track::create($album, $request->get('title'));

        if ($request->has('lyrics')) {
            $this->updateLyrics($track, $request);
        }

        return $this->respondWithItem($track->fresh());
    }

    public function show(Reciter $reciter, Album $album, Track $track): JsonResponse
    {
        event(new TrackViewed($track->id));

        return $this->respondWithItem($track);
    }

    public function update(UpdateTrackRequest $request, Reciter $reciter, Album $album, Track $track): JsonResponse
    {
        if ($request->has('title')) {
            $track->changeTitle($request->get('title'));
        }

        if ($request->has('lyrics')) {
            $this->updateLyrics($track, $request);
        }

        return $this->respondWithItem($track->fresh());
    }

    public function destroy(Reciter $reciter, Album $album, Track $track): Response
    {
        event(new TrackDeleted($track->id));

        return response()->noContent();
    }

    public function uploadTrackMedia(Request $request, Reciter $reciter, Album $album, Track $track): JsonResponse
    {
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

    private function updateLyrics(Track $track, Request $request)
    {
        $document = DocumentFactory::create(
            $request->get('lyrics'),
            new Format($request->get('format', Format::JSON_V1))
        );

        if (!$document->isEmpty()) {
            $track->changeLyrics($document);
        }
    }
}
