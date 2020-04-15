<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Reciter;
use App\Http\Controllers\Controller;
use App\Http\Transformers\ReciterTransformer;
use App\Http\Transformers\TrackTransformer;
use App\Repositories\PopularEntitiesRepository;
use App\Repositories\ReciterRepository;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopularEntitiesController extends Controller
{
    private const CACHE_TTL = 60 * 60 * 24; // 24 hours
    private PopularEntitiesRepository $repository;
    private Cache $cache;

    public function __construct(PopularEntitiesRepository $repository, Cache $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function reciters(Request $request, ReciterTransformer $transformer): JsonResponse
    {
        return $this->cache->remember(
            $this->getRecitersCacheKey($request),
            self::CACHE_TTL,
            fn() => $this->respondWithCollection($this->repository->reciters(), $transformer)
        );
    }

    public function tracks(Request $request, TrackTransformer $transformer, ReciterRepository $reciterRepo): JsonResponse
    {
        $reciter = null;

        if ($request->has('reciterId')) {
            $reciter = $reciterRepo->query()->whereIdentifier($request->get('reciterId'))->get();
        }

        $key = $this->getTracksCacheKey($request, $reciter);

        return $this->cache->remember(
            $key,
            self::CACHE_TTL,
            fn() => $this->respondWithCollection($this->repository->tracks($reciter), $transformer),
        );
    }

    private function getRecitersCacheKey(Request $request): string
    {
        $hash = $this->generateRequestHash($request);
        return "controllers.popular.reciters::{$hash}";
    }

    private function getTracksCacheKey(Request $request, ?Reciter $reciter = null): string
    {
        $key = $reciter ? "controllers.popular.tracks::reciter:{$reciter->getId()}" : 'controllers.popular.tracks';

        $hash = $this->generateRequestHash($request);
        return "$key::{$hash}";
    }

    private function generateRequestHash(Request $request): string
    {
        return md5(http_build_query($request->all()));
    }


}
