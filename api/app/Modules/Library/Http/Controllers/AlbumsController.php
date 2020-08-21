<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Events\Albums\AlbumDeleted;
use App\Modules\Library\Events\Albums\AlbumViewed;
use App\Modules\Library\Http\Requests\CreateAlbumRequest;
use App\Modules\Library\Http\Requests\UpdateAlbumRequest;
use App\Modules\Library\Http\Transformers\AlbumTransformer;
use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AlbumsController extends Controller
{
    public function __construct(AlbumTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function index(Reciter $reciter, Request $request): JsonResponse
    {
        $albums = $reciter->albums()
            ->orderByDesc('year')
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithPaginator($albums);
    }

    public function store(CreateAlbumRequest $request, Reciter $reciter): JsonResponse
    {
        $album = Album::create($reciter, $request->get('title'), $request->get('year'));

        return $this->respondWithItem($album);
    }

    public function show(Reciter $reciter, Album $album): JsonResponse
    {
        event(new AlbumViewed($album->id));

        return $this->respondWithItem($album);
    }

    public function update(UpdateAlbumRequest $request, Reciter $reciter, Album $album): JsonResponse
    {
        if ($request->has('title')) {
            $album->changeTitle($request->get('title'));
        }

        if ($request->has('year')) {
            $album->changeYear($request->get('year'));
        }

        return $this->respondWithItem($album->fresh());
    }

    public function destroy(Reciter $reciter, Album $album): Response
    {
        event(new AlbumDeleted($album->id));

        return response()->noContent();
    }

    public function uploadArtwork(Request $request, Reciter $reciter, Album $album): JsonResponse
    {
        if (!$request->file('artwork')) {
            throw ValidationException::withMessages(['artwork' => 'An artwork file is required.']);
        }

        $path = $request->file('artwork')
            ->storePublicly("reciters/{$reciter->slug}/albums/{$album->year}");

        $album->changeArtwork($path);

        // TODO - handle re-index of tracks

        return $this->respondWithItem($album);
    }
}
