<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Reciter;
use App\Http\Controllers\Controller;
use App\Http\Transformers\ReciterTransformer;
use App\Modules\Library\Actions\CreateReciterAction;
use App\Modules\Library\Actions\SetReciterAvatarAction;
use App\Modules\Library\Actions\UpdateReciterAction;
use App\Modules\Library\Events\ReciterModified;
use App\Repositories\ReciterRepository;
use App\Support\Pagination\PaginationState;
use App\Visits\Manager as VisitsManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $reciters = $this->repository->query()
            ->sortByName()
            ->paginate(PaginationState::fromRequest($request));

        return $this->respondWithPaginator($reciters);
    }

    public function store(Request $request, CreateReciterAction $action): JsonResponse
    {
        $reciter = $action->execute(
            $request->get('name'),
            $request->get('description')
        );

        return $this->respondWithItem($reciter);
    }

    public function show(Reciter $reciter, VisitsManager $visits): JsonResponse
    {
        $visits->record($reciter);
        return $this->respondWithItem($reciter);
    }

    public function update(Request $request, Reciter $reciter, UpdateReciterAction $action): JsonResponse
    {
        $action->execute($reciter, $request->all());
        return $this->respondWithItem($reciter);
    }

    public function uploadAvatar(Request $request, Reciter $reciter, SetReciterAvatarAction $action): JsonResponse
    {
        if (!$request->file('avatar')) {
            throw ValidationException::withMessages(['avatar' => 'An avatar file is required.']);
        }

        $action->execute($reciter, $request->file('avatar'));

        return $this->respondWithItem($reciter);
    }
}
