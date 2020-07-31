<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Http\Transformers;

use App\Modules\Authentication\Models\User;
use App\Modules\Core\Transformers\Transformer;

class UserTransformer extends Transformer
{
    public function toArray(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'nickname' => $user->nickname,
            'avatar' => $user->getAvatar(128),
            'email' => $user->email,
            'role' => $user->role,
            $this->timestamps($user),
        ];
    }
}
