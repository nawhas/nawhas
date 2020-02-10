<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Http\Controllers\Controller;
use App\Http\Transformers\AlbumTransformer;
use App\Repositories\Pagination\PaginationState;
use App\Repositories\AlbumRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlbumsController extends Controller
{
    private AlbumRepository $repository;

    public function __construct(AlbumRepository $repository, AlbumTransformer $transformer)
    {
        $this->repository = $repository;
        $this->transformer = $transformer;
    }

    public function index(Reciter $reciter, Request $request): JsonResponse
    {
        $state = PaginationState::make($request->get('page', 1), $request->get('per_page', 10));
        $reciters = $this->repository->paginateForReciter($reciter, $state);
        return $this->respondWithPaginator($reciters);
    }

    public function show(Reciter $reciter, string $albumId): JsonResponse
    {
        $album = $this->repository->findForReciter($reciter, $albumId);
        return $this->respondWithItem($album);
    }
}
