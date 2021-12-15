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

    public function assertName(string $name): static
    {
        $this->response->assertJsonPath('name', $name);

        return $this;
    }

    public function assertDescription(?string $description): static
    {
        $this->response->assertJsonPath('description', $description);

        return $this;
    }

    public function assertId(string $id): static
    {
        $this->response->assertJsonPath('id', $id);

        return $this;
    }

    public function assertMatches(Reciter $reciter): static
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
