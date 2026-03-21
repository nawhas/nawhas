<?php

declare(strict_types=1);

namespace App\Modules\Stories\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stories\Http\Requests\CreateStoryRequest;
use App\Modules\Stories\Http\Requests\UpdateStoryRequest;
use App\Modules\Stories\Http\Transformers\StoryTransformer;
use App\Modules\Stories\Models\Story;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class StoriesController extends Controller
{
    public function __construct(StoryTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Story::query()
            ->orderByRaw('published_at DESC NULLS LAST')
            ->orderByDesc('updated_at');

        $user = Auth::user();
        if ($user === null || ! $user->can('viewAny', Story::class)) {
            $query->whereNotNull('published_at');
        }

        $paginator = $query->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithPaginator($paginator);
    }

    public function show(Story $story): JsonResponse
    {
        if (! $story->isPublished()) {
            if (! Auth::check() || ! Auth::user()->isModerator()) {
                abort(404);
            }
        }

        $this->authorize('view', $story);

        return $this->respondWithItem($story);
    }

    public function store(CreateStoryRequest $request): JsonResponse
    {
        $this->authorize('create', Story::class);

        $story = Story::create($request->storyPayload());

        return $this->respondWithItem($story);
    }

    public function update(UpdateStoryRequest $request, Story $story): JsonResponse
    {
        $this->authorize('update', $story);

        if ($request->has('title')) {
            $story->changeTitle($request->validated('title'));
        }
        if ($request->has('slug')) {
            $slug = $request->validated('slug');
            if ($slug !== null && $slug !== '') {
                $story->changeSlug($slug);
            }
        }
        if ($request->has('excerpt')) {
            $story->changeExcerpt($request->validated('excerpt'));
        }
        if ($request->has('body')) {
            $story->changeBody($request->validated('body'));
        }
        if ($request->has('hero_image_url')) {
            $story->changeHeroImageUrl($request->validated('hero_image_url'));
        }
        if ($request->has('display_date')) {
            $story->changeDisplayDate($request->validated('display_date'));
        }
        if ($request->has('published')) {
            if ($request->boolean('published')) {
                $story->publish();
            } else {
                $story->unpublish();
            }
        }

        return $this->respondWithItem($story->fresh());
    }

    public function destroy(Story $story): Response
    {
        $this->authorize('delete', $story);

        $story->deleteStory();

        return response()->noContent();
    }
}
