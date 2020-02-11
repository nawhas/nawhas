<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Reciter;
use App\Http\Controllers\Controller;
use App\Http\Transformers\ReciterTransformer;
use App\Repositories\ReciterRepository;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecitersController extends Controller
{
    private ReciterRepository $repository;

    public function __construct(ReciterRepository $repository, ReciterTransformer $transformer)
    {
        $this->transformer = $transformer;
        $this->repository = $repository;
    }

    public function index(Request $request): JsonResponse
    {
        $reciters = $this->repository->paginate(
            PaginationState::fromRequest($request)
        );

        return $this->respondWithPaginator($reciters);
    }

    public function show(Reciter $reciter): JsonResponse
    {
        return $this->respondWithItem($reciter);
    }
}
