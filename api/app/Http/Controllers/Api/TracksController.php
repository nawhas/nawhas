<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Http\Controllers\Controller;
use App\Http\Transformers\TrackTransformer;
use App\Repositories\TrackRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
