<?php


namespace App\Modules\Popular\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Library\Http\Transformers\ReciterTransformer;
use App\Modules\Library\Http\Transformers\TrackTransformer;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use App\Support\Pagination\PaginationState;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopularEntitiesController extends Controller
{
    private const CACHE_TTL = 60 * 60 * 24; // 24 hours
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function reciters(Request $request, ReciterTransformer $transformer): JsonResponse
    {
        return $this->cache->remember(
            $this->getRecitersCacheKey($request),
            self::CACHE_TTL,
            fn() => (
                $this->respondWithCollection(
//                    $this->repository->reciters(PaginationState::fromRequest($request)),
                    Reciter::all(),
                    $transformer
                )
            ),
        );
    }

    public function tracks(Request $request, TrackTransformer $transformer): JsonResponse
    {
        $reciter = null;

        if ($request->has('reciterId')) {
            $reciter = Reciter::retrieve($request->get('reciterId'));
        }

        $key = $this->getTracksCacheKey($request, $reciter);

        return $this->cache->remember(
            $key,
            self::CACHE_TTL,
            fn() => (
                $this->respondWithCollection(
//                    $this->repository->tracks(PaginationState::fromRequest($request), $reciter),
                    Track::all(),
                    $transformer
                )
            ),
        );
    }

    private function getRecitersCacheKey(Request $request): string
    {
        $hash = $this->generateRequestHash($request);
        return "controllers.popular.reciters::{$hash}";
    }

    private function getTracksCacheKey(Request $request, ?Reciter $reciter = null): string
    {
        $key = $reciter ? "controllers.popular.tracks::reciter:{$reciter->id}" : 'controllers.popular.tracks';

        $hash = $this->generateRequestHash($request);
        return "$key::{$hash}";
    }

    private function generateRequestHash(Request $request): string
    {
        return md5(http_build_query($request->all()));
    }
}
