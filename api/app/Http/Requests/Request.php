<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Entities\User;
use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function user($guard = null): ?User
    {
        return parent::user($guard);
    }
}
