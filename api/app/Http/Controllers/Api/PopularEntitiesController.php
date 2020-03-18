<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\AlbumTransformer;
use App\Http\Transformers\ReciterTransformer;
use App\Http\Transformers\TrackTransformer;
use App\Queries\AlbumQuery;
use App\Queries\TrackQuery;
use App\Repositories\AlbumRepository;
use App\Repositories\PopularEntitiesRepository;
use App\Repositories\ReciterRepository;
use App\Repositories\TrackRepository;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopularEntitiesController extends Controller
{
    private PopularEntitiesRepository $repository;

    public function __construct(PopularEntitiesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function reciters(Request $request, ReciterTransformer $transformer): JsonResponse
    {
        return $this->respondWithCollection($this->repository->reciters(), $transformer);
    }

    public function tracks(Request $request, TrackTransformer $transformer, ReciterRepository $reciterRepo): JsonResponse
    {
        $reciter = null;

        if ($request->has('reciterId')) {
            $reciter = $reciterRepo->query()->whereIdentifier($request->get('reciterId'))->get();
        }

        return $this->respondWithCollection($this->repository->tracks($reciter), $transformer);
    }
}
