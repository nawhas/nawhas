<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\AlbumTransformer;
use App\Http\Transformers\ReciterTransformer;
use App\Http\Transformers\TrackTransformer;
use App\Repositories\AlbumRepository;
use App\Repositories\ReciterRepository;
use App\Repositories\TrackRepository;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopularEntitiesController extends Controller
{
    public function reciters(Request $request, ReciterTransformer $transformer, ReciterRepository $repository): JsonResponse
    {
        $reciters = $repository->query()
            ->sortRandom()
            ->paginate(PaginationState::fromRequest($request));

        return $this->respondWithPaginator($reciters, $transformer);
    }

    public function albums(Request $request, AlbumTransformer $transformer, ReciterRepository $reciterRepo, AlbumRepository $albumRepo): JsonResponse
    {
        $query = $albumRepo->query()
            ->sortRandom();

        if ($request->has('reciterId')) {
            $reciter = $reciterRepo->query()->whereIdentifier($request->get('reciterId'))->get();
            $query->whereReciter($reciter);
        }

        $albums = $query->paginate(PaginationState::fromRequest($request));

        return $this->respondWithPaginator($albums, $transformer);
    }

    public function tracks(Request $request, TrackTransformer $transformer, ReciterRepository $reciterRepo, TrackRepository $trackRepo): JsonResponse
    {
        $query = $trackRepo->query()
            ->sortRandom();

        if ($request->has('reciterId')) {
            $reciter = $reciterRepo->query()->whereIdentifier($request->get('reciterId'))->get();
            $query->whereReciter($reciter);
        }

        $tracks = $query->paginate(PaginationState::fromRequest($request));

        return $this->respondWithPaginator($tracks, $transformer);
    }
}
