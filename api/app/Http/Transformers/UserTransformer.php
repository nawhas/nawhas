<?php

declare(strict_types=1);

namespace App\Http\Transformers;

use App\Entities\User;

class UserTransformer extends Transformer
{
    public function toArray(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'nickname' => $user->getNickname(),
            'avatar' => $user->getAvatar("128"),
            'email' => $user->getEmail(),
            'role' => $user->getRole()->getValue(),
            $this->timestamps($user),
        ];
    }
}
