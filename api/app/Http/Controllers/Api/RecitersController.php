<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Reciter;
use App\Http\Controllers\Controller;
use App\Http\Transformers\ReciterTransformer;
use App\Modules\Library\Events\ReciterCreated;
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

    public function store(Request $request): JsonResponse
    {
        $reciter = new Reciter(
            $request->get('name'),
            $request->get('description')
        );

        event(new ReciterCreated($reciter));

        $this->repository->persist($reciter);

        return $this->respondWithItem($reciter);
    }

    public function show(Reciter $reciter, VisitsManager $visits): JsonResponse
    {
        $visits->record($reciter);
        return $this->respondWithItem($reciter);
    }

    public function update(Request $request, Reciter $reciter): JsonResponse
    {
        if ($request->has('name')) {
            $reciter->setName($request->get('name'));
        }

        if ($request->has('description')) {
            $reciter->setDescription($request->get('description'));
        }

        event(new ReciterModified($reciter));

        $this->repository->persist($reciter);

        return $this->respondWithItem($reciter);
    }

    public function uploadAvatar(Request $request, Reciter $reciter): JsonResponse
    {
        if (!$request->file('avatar')) {
            throw ValidationException::withMessages(['avatar' => 'An avatar file is required.']);
        }

        $existing = $reciter->getAvatar();
        $path = $request->file('avatar')->storePublicly("reciters/{$reciter->getSlug()}");
        $reciter->setAvatar($path);

        if ($existing !== null) {
            Storage::delete($existing);
        }

        $this->repository->persist($reciter);

        return $this->respondWithItem($reciter);
    }
}
