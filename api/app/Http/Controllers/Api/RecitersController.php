<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Reciter;
use App\Http\Controllers\Controller;
use App\Http\Transformers\ReciterTransformer;
use App\Repositories\ReciterRepository;
use App\Support\Pagination\PaginationState;
use Illuminate\Support\Facades\Storage;
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
        $reciters = $this->repository->query()
            ->sortByName()
            ->paginate(PaginationState::fromRequest($request));

        return $this->respondWithPaginator($reciters);
    }

    public function show(Reciter $reciter): JsonResponse
    {
        return $this->respondWithItem($reciter);
    }

    public function update(Request $request, Reciter $reciter): JsonResponse
    {
        if ($request->has('name')) {
            $reciter->setName($request->get('name'));
        }

        $this->repository->persist($reciter);

        return $this->respondWithItem($reciter);
    }

    public function uploadAvatar(Request $request, Reciter $reciter): JsonResponse
    {
        if ($request->file('avatar')) {
            $existing = $reciter->getAvatar();

            \Image::make($request->file('avatar'))->resize(512, null, fn ($constraint) => $constraint->aspectRatio())->save();
            // $extension = $request->file('avatar')->getClientOriginalExtension();
            // $path = Storage::put("reciters/{$reciter->getSlug()}/avatar." . $extension, $request->file('avatar'), 'public');
            $path = $request->file('avatar')->storePublicly("reciters/{$reciter->getSlug()}");
            $reciter->setAvatar($path);

            if ($existing !== null) {
                Storage::delete($existing);
            }
        }

        $this->repository->persist($reciter);

        return $this->respondWithItem($reciter);
    }
}
