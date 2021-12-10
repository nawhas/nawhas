<?php

declare(strict_types=1);

namespace App\Modules\Features\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Features\FeatureManager;
use Illuminate\Http\JsonResponse;

class FeaturesController extends Controller
{
    public function __construct(
        private FeatureManager $features
    ) {}

    public function index(): JsonResponse
    {
        return $this->respondWithArray([
            'data' => $this->features->export(),
        ]);
    }
}
