<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Http\Controllers\Controller;
use App\Http\Transformers\AlbumTransformer;
use App\Repositories\AlbumRepository;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlbumsController extends Controller
{
    private AlbumRepository $repository;

    public function __construct(AlbumRepository $repository, AlbumTransformer $transformer)
    {
        $this->transformer = $transformer;
        $this->repository = $repository;
    }

    public function index(Reciter $reciter, Request $request): JsonResponse
    {
        $albums = $this->repository->query()
            ->whereReciter($reciter)
            ->sortByNewest()
            ->paginate(PaginationState::fromRequest($request));

        return $this->respondWithPaginator($albums);
    }

    public function show(Reciter $reciter, Album $album): JsonResponse
    {
        return $this->respondWithItem($album);
    }
}
