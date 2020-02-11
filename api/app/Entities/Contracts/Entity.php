<?php declare(strict_types=1);

namespace App\Entities\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

interface Entity extends Arrayable, Jsonable, JsonSerializable
{

}
