<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

class UserResponse extends Response
{
    public function assertUserIdMatches(string $id): self
    {
        $this->response->assertJsonPath('id', $id);

        return $this;
    }

    public function assertNameMatches(string $name): self
    {
        $this->response->assertJsonPath('name', $name);

        return $this;
    }

    public function assertEmailMatches(string $email): self
    {
        $this->response->assertJsonPath('email', $email);

        return $this;
    }

    protected function getJsonStructure(): array
    {
        return [
            'id',
            'name',
            'nickname',
            'avatar',
            'email',
            'role',
            'createdAt',
            'updatedAt',
        ];
    }
}
