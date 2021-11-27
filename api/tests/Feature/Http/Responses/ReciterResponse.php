<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Modules\Library\Models\Reciter;

class ReciterResponse extends Response
{
    protected function getJsonStructure(): array
    {
        return [
            'id',
            'name',
            'slug',
            'avatar',
            'description',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertName(string $name): self
    {
        $this->response->assertJsonPath('name', $name);

        return $this;
    }

    /**
     * TODO:PHP8 - Replace self with static
     * @return static
     */
    public function assertMatches(Reciter $reciter): self
    {
        $this->response->assertJsonFragment([
            'id' => $reciter->id,
            'name' => $reciter->name,
            'description' => $reciter->description,
            'slug' => $reciter->slug,
        ]);

        return $this;
    }
}
