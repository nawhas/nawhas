<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Authentication\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\Feature\FeatureTest;

abstract class HttpTest extends FeatureTest
{
    private ?User $contributor;
    private ?User $moderator;

    /**
     * @param callable(): \Illuminate\Testing\TestResponse $request
     */
    public function assertRequiresModeratorAuthentication(callable $request): TestResponse
    {
        // Unauthenticated
        $request()->assertUnauthorized();

        // As a contributor
        $this->asContributor();
        $request()->assertForbidden();

        // As a moderator
        $this->asModerator();
        return $request()->assertSuccessful();
    }

    protected function asModerator(): static
    {
        $this->actingAs($this->moderator());

        return $this;
    }

    protected function asContributor(): static
    {
        $this->actingAs($this->contributor());

        return $this;
    }

    protected function moderator(): User
    {
        return $this->moderator ?? $this->getUserFactory()->moderator();
    }

    protected function contributor(): User
    {
        return $this->contributor ?? $this->getUserFactory()->contributor();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->contributor = null;
        $this->moderator = null;
    }
}
