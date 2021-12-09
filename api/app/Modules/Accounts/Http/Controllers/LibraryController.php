<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounts\Events\Saves\SavedTrackRemoved;
use App\Modules\Accounts\Events\Saves\TrackSaved;
use App\Modules\Accounts\Http\Requests\RemoveSavedTracksRequest;
use App\Modules\Accounts\Http\Requests\SaveTracksRequest;
use App\Modules\Authentication\Models\User;
use App\Modules\Library\Http\Transformers\TrackTransformer;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class LibraryController extends Controller
{
    public function tracks(Request $request): JsonResponse
    {
        $tracks = $this->getUser()->savedTracks()
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithPaginator($tracks, new TrackTransformer());
    }

    public function getTrackIds()
    {
        $tracks = $this->getUser()->savedTracks()->get(['saveable_id'])->map(function ($result) {
            return $result->saveable_id;
        })->unique()->values();
        return $this->respondWithArray($tracks->all());
    }

    public function saveTracks(SaveTracksRequest $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $this->prepareIds($request->get('ids'))
            ->each(function (string $id) use ($user) {
                if ($user->hasSavedTrack($id)) {
                    return;
                }
                event(new TrackSaved($id));
            });

        return response()->noContent();
    }

    public function removeSavedTracks(RemoveSavedTracksRequest $request)
    {
        $this->prepareIds($request->get('ids'))
            ->each(fn (string $id) => (bool)event(new SavedTrackRemoved($id)));

        return response()->noContent();
    }

    private function prepareIds(array $ids): Collection
    {
        return collect($ids)
            ->filter()
            ->unique();
    }
}
