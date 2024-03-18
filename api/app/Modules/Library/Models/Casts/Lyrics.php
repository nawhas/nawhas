<?php

declare(strict_types=1);

namespace App\Modules\Library\Models\Casts;

use App\Modules\Lyrics\Documents\Document;
use App\Modules\Lyrics\Documents\Factory;
use App\Modules\Lyrics\Documents\Format;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * @implements CastsAttributes<Document,string|Document>
 */
class Lyrics implements CastsAttributes
{
    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return Document|null
     * @throws \JsonException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Document
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);
        return Factory::create($data['content'], Format::from($data['format']));
    }

    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
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
