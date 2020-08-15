<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Events\Playlists\PlaylistDeleted;
use App\Modules\Library\Events\Playlists\PlaylistNameChanged;
use App\Modules\Library\Events\Playlists\PlaylistTrackRemoved;
use App\Modules\Library\Http\Transformers\PlaylistTransformer;
use App\Modules\Library\Models\Playlist;
use App\Modules\Library\Models\PlaylistTracks;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlaylistsController extends Controller
{
    public function __construct(PlaylistTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function index(Request $request):JsonResponse
    {
        $playlists = Playlist::query()
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithPaginator($playlists);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO - need to change the request to a customised request
        $playlist = Playlist::create(
            $request->get('name'),
            $request->get('track_id'),
        );

        return $this->respondWithItem($playlist);
    }

    public function show(Playlist $playlist): JsonResponse
    {
        return $this->respondWithItem($playlist);
    }

    public function update(Request $request, Playlist $playlist): JsonResponse
    {
        if ($playlist->name !== $request->get('name')) {
            event(new PlaylistNameChanged($playlist->id, $request->get('name')));
        }

        return $this->respondWithItem($playlist->fresh());
    }

    public function delete(Playlist $playlist): Response
    {
        event(new PlaylistDeleted($playlist->id));

        return response()->noContent();
    }

    public function addTrack(Request $request, Playlist $playlist): JsonResponse
    {
        PlaylistTracks::create($playlist->id, $request->get('track_id'));

        return $this->respondWithItem($playlist->fresh());
    }

    public function removeTrack(Playlist $playlist, PlaylistTracks $playlistTracks): Response
    {
        event(new PlaylistTrackRemoved($playlistTracks->id));

        return response()->noContent();
    }
}
