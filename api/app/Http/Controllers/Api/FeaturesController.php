<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Features\FeatureManager;
use Illuminate\Http\JsonResponse;

class FeaturesController extends Controller
{
    private FeatureManager $features;

    public function __construct(FeatureManager $features)
    {
        $this->features = $features;
    }

    public function index(): JsonResponse
    {
        return $this->respondWithArray([
            'data' => $this->features->export(),
        ]);
    }

    public function secret(): JsonResponse
    {
        return $this->respondWithArray([
            'data' => 'You found me!',
        ]);
    }
}
