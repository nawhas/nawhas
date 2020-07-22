<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Events\Reciters\ReciterDeleted;
use App\Modules\Library\Http\Requests\{CreateReciterRequest, UpdateReciterRequest};
use App\Modules\Library\Http\Resources\{ReciterCollection, ReciterResource};
use App\Modules\Library\Models\Reciter;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class RecitersController extends Controller
{
    public function store(CreateReciterRequest $request): JsonResource
    {
        $reciter = Reciter::create(
            $request->name(),
            $request->description(),
        );

        return new ReciterResource($reciter);
    }

    public function index(Request $request): JsonResource
    {
        $reciters = Reciter::query()->orderBy('name')
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return new ReciterCollection($reciters);
    }

    public function show(string $id): JsonResource
    {
        $reciter = Reciter::show($id);

        return new ReciterResource($reciter);
    }

    public function update(string $id, UpdateReciterRequest $request): JsonResource
    {
        $reciter = Reciter::retrieve($id);

        if ($request->has('name')) {
            $reciter->changeName($request->name());
        }

        if ($request->has('description')) {
            $reciter->changeDescription($request->description());
        }

        return new ReciterResource($reciter->fresh());
    }

    public function uploadAvatar(string $id, Request $request): JsonResource
    {
        $reciter = Reciter::retrieve($id);

        if (!$request->file('avatar')) {
            throw ValidationException::withMessages(['avatar' => 'An avatar file is required.']);
        }

        $path = $request->file('avatar')->storePublicly("reciters/{$reciter->slug}");

        $reciter->changeAvatar($path);

        return new ReciterResource($reciter->fresh());
    }

    public function delete(string $id): Response
    {
        event(new ReciterDeleted($id));

        return response()->noContent();
    }
}
