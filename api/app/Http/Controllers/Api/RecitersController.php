<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Reciter;
use App\Http\Controllers\Controller;
use App\Http\Transformers\ReciterTransformer;
use App\Repositories\Pagination\PaginationState;
use App\Repositories\ReciterRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecitersController extends Controller
{
    private ReciterRepository $repository;

    public function __construct(ReciterRepository $repository, ReciterTransformer $transformer)
    {
        $this->repository = $repository;
        $this->transformer = $transformer;
    }

    public function index(Request $request): JsonResponse
    {
        $state = PaginationState::make($request->get('page', 1), $request->get('per_page', 10));
        $reciters = $this->repository->paginateAll($state);
        return $this->respondWithPaginator($reciters);
    }

    public function show(string $id): JsonResponse
    {
        $reciter = $this->repository->findOrFail($id);
        return $this->respondWithItem($reciter);
    }
}
