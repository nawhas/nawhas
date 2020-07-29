<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Events\Reciters\ReciterDeleted;
use App\Modules\Library\Events\Reciters\ReciterViewed;
use App\Modules\Library\Http\Transformers\ReciterTransformer;
use App\Modules\Library\Http\Requests\{CreateReciterRequest, UpdateReciterRequest};
use App\Modules\Library\Models\Reciter;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\{JsonResponse, Request, Response};
use Illuminate\Validation\ValidationException;

class RecitersController extends Controller
{
    public function __construct(ReciterTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function store(CreateReciterRequest $request): JsonResponse
    {
        $reciter = Reciter::create(
            $request->name(),
            $request->description(),
        );

        return $this->respondWithItem($reciter);
    }

    public function index(Request $request): JsonResponse
    {
        $reciters = Reciter::query()->orderBy('name')
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithPaginator($reciters);
    }

    public function show(Reciter $reciter): JsonResponse
    {
        event(new ReciterViewed($reciter->id));

        return $this->respondWithItem($reciter);
    }

    public function update(Reciter $reciter, UpdateReciterRequest $request): JsonResponse
    {
        if ($request->has('name')) {
            $reciter->changeName($request->name());
        }

        if ($request->has('description')) {
            $reciter->changeDescription($request->description());
        }

        return $this->respondWithItem($reciter->fresh());
    }

    public function uploadAvatar(Reciter $reciter, Request $request): JsonResponse
    {
        if (!$request->file('avatar')) {
            throw ValidationException::withMessages(['avatar' => 'An avatar file is required.']);
        }

        $path = $request->file('avatar')->storePublicly("reciters/{$reciter->slug}");

        $reciter->changeAvatar($path);

        return $this->respondWithItem($reciter->fresh());
    }

    public function delete(Reciter $reciter): Response
    {
        event(new ReciterDeleted($reciter->id));

        return response()->noContent();
    }
}
