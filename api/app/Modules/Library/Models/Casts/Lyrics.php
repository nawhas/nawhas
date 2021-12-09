<?php

declare(strict_types=1);

namespace App\Modules\Library\Models\Casts;

use App\Modules\Lyrics\Documents\Document;
use App\Modules\Lyrics\Documents\Factory;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class Lyrics implements CastsAttributes
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return \App\Modules\Lyrics\Documents\Document|null
     * @throws \JsonException
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);
        return Factory::create($data['content'], new Format($data['format']));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        if (!($value instanceof Document)) {
            throw new InvalidArgumentException('The given value is not a Document instance.');
        }

        return json_encode($value->toArray());
    }
}
