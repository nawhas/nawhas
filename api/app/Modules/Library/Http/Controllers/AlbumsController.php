<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Events\Albums\AlbumDeleted;
use App\Modules\Library\Events\Albums\AlbumViewed;
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

    public function index(string $reciterId, Request $request): JsonResponse
    {
        $reciter = Reciter::retrieve($reciterId);

        $albums = $reciter->albums()
            ->orderByDesc('year')
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithPaginator($albums);
    }

    public function store(Request $request, string $reciterId): JsonResponse
    {
        $reciter = Reciter::retrieve($reciterId);
        $album = Album::create($reciter, $request->get('title'), $request->get('year'));

        return $this->respondWithItem($album);
    }

    public function show(string $reciterId, string $albumId): JsonResponse
    {
        $reciter = Reciter::retrieve($reciterId);
        $album = Album::retrieve($albumId, $reciter->id);

        event(new AlbumViewed($album->id));

        return $this->respondWithItem($album);
    }

    public function update(Request $request, string $reciterId, string $albumId): JsonResponse
    {
        $reciter = Reciter::retrieve($reciterId);
        $album = Album::retrieve($albumId, $reciter->id);

        if ($request->has('title')) {
            $album->changeTitle($request->get('title'));
        }

        if ($request->has('year')) {
            $album->changeTitle($request->get('year'));
        }

        return $this->respondWithItem($album->fresh());
    }

    public function destroy(string $reciterId, string $albumId): Response
    {
        $reciter = Reciter::retrieve($reciterId);
        $album = Album::retrieve($albumId, $reciter->id);

        event(new AlbumDeleted($album->id));

        return response()->noContent();
    }

    public function uploadArtwork(Request $request, string $reciterId, string $albumId): JsonResponse
    {
        $reciter = Reciter::retrieve($reciterId);
        $album = Album::retrieve($albumId, $reciter->id);

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
