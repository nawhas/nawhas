<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Http\Requests\RedirectRequest;
use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Aliases\AlbumAlias;
use App\Modules\Library\Models\Aliases\ReciterAlias;
use App\Modules\Library\Models\Aliases\TrackAlias;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class RedirectController extends Controller
{
    public function redirect(RedirectRequest $request): JsonResponse
    {
        $reciter = $this->determineReciter($request->get('reciter'));

        if ($request->has('album')) {
            $album = $this->determineAlbum($reciter, $request->get('album'));

            if ($request->has('track')) {
                $track = $this->determineTrack($reciter, $album, $request->get('track'));

                return $this->respondWithArray([
                   'to' => $track->getUrlPath(),
                ]);
            }

            return $this->respondWithArray([
               'to' => $album->getUrlPath(),
            ]);
        }

        return $this->respondWithArray([
            'to' => $reciter->getUrlPath(),
        ]);
    }

    private function determineReciter(string $slug): Reciter
    {
        /** @var Reciter|null $reciter */
        $reciter = Reciter::where('slug', $slug)->first();

        if ($reciter) {
            return $reciter;
        }

        /** @var ReciterAlias|null $alias */
        $alias = ReciterAlias::query()
            ->where('alias', $slug)
            ->first();

        if ($alias) {
            return $alias->reciter;
        }

        throw new ModelNotFoundException("No reciter found for '{$slug}'");
    }

    private function determineAlbum(Reciter $reciter, string $year): Album
    {
        /** @var Album|null $album */
        $album = $reciter->albums()->where('year', $year)->first();

        if ($album) {
            return $album;
        }

        /** @var AlbumAlias|null $alias */
        $alias = AlbumAlias::query()
            ->where('reciter_id', $reciter->id)
            ->where('alias', $year)
            ->first();

        if ($alias) {
            return $alias->album;
        }

        throw new ModelNotFoundException("No album found for '{$year}'");
    }

    private function determineTrack(Reciter $reciter, Album $album, string $slug): Track
    {
        /** @var Track|null $track */
        $track = $album->tracks()->where('slug', $slug)->first();

        if ($track) {
            return $track;
        }

        /** @var TrackAlias|null $alias */
        $alias = TrackAlias::query()
            ->where('reciter_id', $reciter->id)
            ->where('album_id', $album->id)
            ->where('alias', $slug)
            ->first();

        if ($alias) {
            return $alias->track;
        }

        throw new ModelNotFoundException("No track found for '{$slug}'");
    }
}
