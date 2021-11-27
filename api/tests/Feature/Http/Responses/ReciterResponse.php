<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use Illuminate\Testing\Assert;

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

    public function assertName(string $name): self
    {
        $this->response->assertJsonPath('name', $name);

        return $this;
    }
}
