<?php

declare(strict_types=1);

namespace App\Modules\Audit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Http\Transformers\RevisionTransformer;
use App\Modules\Audit\Models\Revision;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevisionsController extends Controller
{
    public function __construct(RevisionTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function index(Request $request): JsonResponse
    {
        $revisions = Revision::query()
            ->orderByDesc('created_at')
            ->orderByDesc('version')
            ->with(['entity', 'user'])
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithPaginator($revisions);
    }
}
