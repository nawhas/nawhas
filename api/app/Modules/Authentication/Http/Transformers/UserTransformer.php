<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Http\Transformers;

use App\Http\Transformers\Transformer;
use App\Modules\Authentication\Models\User;

class UserTransformer extends Transformer
{
    public function toArray(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'nickname' => $user->nickname,
            'avatar' => $user->avatar,
            'email' => $user->email,
            'role' => $user->role,
            $this->timestamps($user),
        ];
    }
}
