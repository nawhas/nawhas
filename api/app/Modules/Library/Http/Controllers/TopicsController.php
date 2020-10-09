<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Http\Requests\CreateTopicRequest;
use App\Modules\Library\Http\Transformers\TopicTransformer;
use App\Modules\Library\Models\Topic;
use App\Support\Pagination\PaginationState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
    public function __construct(TopicTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function index(Request $request): JsonResponse
    {
        $topics = Topic::query()
            ->orderBy('name')
            ->paginate(PaginationState::fromRequest($request)->getLimit());

        return $this->respondWithCollection($topics);
    }

    public function show(Topic $topic): JsonResponse
    {
        return $this->respondWithItem($topic);
    }

    public function store(CreateTopicRequest $request): JsonResponse
    {
        $topic = Topic::create(
            $request->get('name'),
            $request->get('description')
        );

        return $this->respondWithItem($topic);
    }
}
