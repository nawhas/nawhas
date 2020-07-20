<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\Album;
use App\Entities\Lyrics;
use App\Entities\Reciter;
use App\Entities\Track;
use App\Entities\Media;
use App\Http\Controllers\Controller;
use App\Http\Transformers\TrackTransformer;
use App\Modules\Library\Events\LyricsCreated;
use App\Modules\Library\Events\LyricsDeleted;
use App\Modules\Library\Events\LyricsModified;
use App\Modules\Library\Events\TrackCreated;
use App\Modules\Library\Events\TrackDeleted;
use App\Modules\Library\Events\TrackModified;
use App\Modules\Lyrics\Documents\Factory as DocumentFactory;
use App\Modules\Lyrics\Documents\Format;
use App\Repositories\TrackRepository;
use App\Visits\Manager as VisitsManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use JsonException;

class TracksController extends Controller
{
    private TrackRepository $repository;

    public function __construct(TrackRepository $repository, TrackTransformer $transformer)
    {
        $this->transformer = $transformer;
        $this->repository = $repository;
    }

    public function index(Reciter $reciter, Album $album): JsonResponse
    {
        $tracks = $this->repository->allFromAlbum($album);

        return $this->respondWithCollection($tracks);
    }

    public function store(Request $request, Reciter $reciter, Album $album): JsonResponse
    {
        $track = new Track(
            $album,
            $request->get('title')
        );

        if ($request->has('lyrics')) {
            $format = $request->get('format', Format::PLAIN_TEXT);
            $lyric = new Lyrics($track, $request->get('lyrics'), new Format($format));
            $track->replaceLyrics($lyric);
            event(new LyricsCreated($lyric));
        }

        event(new TrackCreated($track));

        $this->repository->persist($track);

        return $this->respondWithItem($track);
    }

    public function show(Reciter $reciter, Album $album, Track $track, VisitsManager $visits): JsonResponse
    {
        $visits->record($reciter, $track);

        return $this->respondWithItem($track);
    }

    public function update(Request $request, Reciter $reciter, Album $album, Track $track): JsonResponse
    {
        if ($request->has('title')) {
            $track->setTitle($request->get('title'));
        }
        if ($request->has('lyrics')) {
            $this->updateLyrics($track, $request->get('lyrics'), (int)$request->get('format', Format::PLAIN_TEXT));
        }

        event(new TrackModified($track));

        $this->repository->persist($track);

        return $this->respondWithItem($track);
    }

    public function destroy(Request $request, Reciter $reciter, Album $album, Track $track): Response
    {
        // TODO - Use events to handle this sort of stuff
        if ($track->hasAudioFile()) {
            $media = $track->getAudioFile();
            logger()->debug("Deleting media file at {$media->getPath()}");
            Storage::delete($media->getPath());
        }

        event(new TrackDeleted($track));

        $this->repository->remove($track);

        return response()->noContent();
    }

    public function uploadTrackMedia(Request $request, Reciter $reciter, Album $album, Track $track): JsonResponse
    {
        if (!$request->file('audio')) {
            throw ValidationException::withMessages(['audio' => 'An audio file is required.']);
        }

        $existing = $track->getAudioFile();

        $path = $request->file('audio')->storePublicly("reciters/{$reciter->getSlug()}/albums/{$album->getYear()}/tracks/{$track->getSlug()}");
        $track->addAudioFile(Media::audioFile($path));

        if ($existing !== null) {
            Storage::delete($existing->getPath());
        }

        event(new TrackModified($track));

        $this->repository->persist($track);

        return $this->respondWithItem($track);
    }

    private function updateLyrics(Track $track, ?string $content, int $format): void
    {
        if ($content === null) {
            return;
        }

        $old = $track->getLyrics();

        try {
            $document = DocumentFactory::create($content, new Format($format));
        } catch (JsonException  $e) {
            throw ValidationException::withMessages(['lyrics' => 'The lyrics content is invalid.']);
        }

        // If there's no old version of lyrics, and the new document is empty, ignore this change.
        if ($old === null && $document->isEmpty()) {
            return;
        }

        // If an old version exists, and the new version is empty, we need to delete the old version.
        if ($document->isEmpty()) {
            $track->removeLyrics();
            event(new LyricsDeleted($old));
            return;
        }

        // Otherwise, add new lyrics.
        $lyrics = new Lyrics($track, $content, new Format($format));
        $track->replaceLyrics($lyrics);

        if ($old === null) {
            event(new LyricsCreated($lyrics));
        } else {
            event(new LyricsModified($lyrics, $old));
        }
    }
}
