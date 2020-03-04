<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Album;
use App\Entities\Lyrics;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Entities\Media;
use App\Http\Controllers\Controller;
use App\Http\Transformers\TrackTransformer;
use App\Repositories\TrackRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TracksController extends Controller
{
    private TrackRepository $repository;

    public function __construct(TrackRepository $repository, TrackTransformer $transformer)
    {
        $this->transformer = $transformer;
        $this->repository = $repository;
    }

    public function index(Reciter $reciter, Album $album, Request $request): JsonResponse
    {
        $tracks = $this->repository->allFromAlbum($album);

        return $this->respondWithCollection($tracks);
    }

    public function show(Reciter $reciter, Album $album, Track $track): JsonResponse
    {
        return $this->respondWithItem($track);
    }

    public function update(Request $request, Reciter $reciter, Album $album, Track $track): JsonResponse
    {
        if ($request->has('title')) {
            $track->setTitle($request->get('title'));
        }
        if ($request->has('lyrics')) {
            $track->replaceLyrics(new Lyrics($track, $request->get('lyrics')));
        }

        $this->repository->persist($track);

        return $this->respondWithItem($track);
    }

    public function uploadTrackMedia(Request $request, Reciter $reciter, Album $album, Track $track): JsonResponse
    {
        if (!$request->file('audio')) {
            throw ValidationException::withMessages(['audio' => 'An audio file is required.']);
        }

        $existing = $track->getAudioFile();

        $path = $request->file('audio')->storePublicly("reciters/{$reciter->getSlug()}/albums/{$album->getYear()}/tracks/{$track->getSlug()}");
        $track->addAudioFile(Media::audioFile($path));

        if ($existing !== null) {
            Storage::delete($existing->getPath());
        }

        $this->repository->persist($track);

        return $this->respondWithItem($track);
    }
}
