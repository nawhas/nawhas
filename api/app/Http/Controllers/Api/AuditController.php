<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Repositories\AuditRepository;
use App\Modules\Audit\Transformers\AuditRecordTransformer;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    private AuditRepository $repository;

    public function __construct(AuditRepository $repository, AuditRecordTransformer $transformer)
    {
        $this->transformer = $transformer;
        $this->repository = $repository;
    }

    public function index(Request $request): JsonResponse
    {
        $records = $this->repository->paginate(PaginationState::fromRequest($request));

        return $this->respondWithPaginator($records);
    }
}
