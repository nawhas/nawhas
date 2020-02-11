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
        $reciters = $repository->query()->paginate(PaginationState::make(1, (int)$request->get('limit', 10)));

        return $this->respondWithPaginator($reciters, $transformer);
    }

    public function albums(Request $request, AlbumTransformer $transformer, AlbumRepository $repository): JsonResponse
    {
        $albums = $repository->query()->paginate(PaginationState::make(1, (int)$request->get('limit', 10)));

        return $this->respondWithPaginator($albums, $transformer);
    }

    public function tracks(Request $request, TrackTransformer $transformer, TrackRepository $repository): JsonResponse
    {
        $tracks = $repository->query()->paginate(PaginationState::make(1, (int)$request->get('limit', 10)));

        return $this->respondWithPaginator($tracks, $transformer);
    }
}
