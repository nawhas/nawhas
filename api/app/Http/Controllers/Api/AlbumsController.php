<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Http\Controllers\Controller;
use App\Http\Transformers\AlbumTransformer;
use App\Repositories\AlbumRepository;
use App\Repositories\TrackRepository;
use App\Support\Pagination\PaginationState;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Zain\LaravelDoctrine\Algolia\SearchService;

class AlbumsController extends Controller
{
    private AlbumRepository $repository;

    public function __construct(AlbumRepository $repository, AlbumTransformer $transformer)
    {
        $this->transformer = $transformer;
        $this->repository = $repository;
    }

    public function index(Reciter $reciter, Request $request): JsonResponse
    {
        $albums = $this->repository->query()
            ->whereReciter($reciter)
            ->sortByNewest()
            ->paginate(PaginationState::fromRequest($request));

        return $this->respondWithPaginator($albums);
    }

    public function store(Request $request, Reciter $reciter): JsonResponse
    {
        $album = new Album($reciter, $request->get('title'), $request->get('year'));

        $this->repository->persist($album);

        return $this->respondWithItem($album);
    }

    public function show(Reciter $reciter, Album $album): JsonResponse
    {
        return $this->respondWithItem($album);
    }

    public function update(Request $request, Reciter $reciter, Album $album): JsonResponse
    {
        if ($request->has('title')) {
            $album->setTitle($request->get('title'));
        }

        if ($request->has('year')) {
            $album->setYear($request->get('year'));
        }

        $this->repository->persist($album);

        return $this->respondWithItem($album);
    }

    public function destroy(Request $request, Reciter $reciter, Album $album, TrackRepository $trackRepository): Response
    {
        if ($album->hasArtwork()) {
            $media = $album->getArtwork();
            logger()->debug("Deleting media file at {$media->getPath()}");
            Storage::delete($media->getPath());
        }

        foreach($album->getTracks() as $track) {
            if ($track->hasAudioFile()) {
                $media = $track->getAudioFile();
                logger()->debug("Deleting media file at {$media->getPath()}");
                Storage::delete($media->getPath());
            }
    
            $trackRepository->remove($track);
        }

        $this->repository->remove($album);

        return response()->noContent();
    }

    public function uploadArtwork(Request $request, Reciter $reciter, Album $album, SearchService $search): JsonResponse
    {
        if (!$request->file('artwork')) {
            throw ValidationException::withMessages(['artwork' => 'An artwork file is required.']);
        }

        $existing = $album->getArtwork();
        $path = $request->file('artwork')->storePublicly("reciters/{$reciter->getSlug()}/albums/{$album->getYear()}");
        $album->setArtwork($path);

        if ($existing !== null) {
            Storage::delete($existing);
        }

        $this->repository->persist($album);

        // Re-index associated tracks.
        $search->index(app('em'), $album->getTracks()->toArray());

        return $this->respondWithItem($album);
    }
}
