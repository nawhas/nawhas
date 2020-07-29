<?php

declare(strict_types=1);

namespace App\Modules\Popular\Models;

use App\Modules\Popular\Events\ModelVisited;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ramsey\Uuid\Uuid;

class Visit extends Model
{
    protected $fillable = ['ip', 'date', 'visitable_id', 'visitable_type'];

    public static function create(string $ip, string $date, string $visitableId, string $visitableType): self
    {
        $id = Uuid::uuid1()->toString();

        event(new ModelVisited($id, [
            'ip' => $ip,
            'date' => $date,
            'visitableId' => $visitableId,
            'visitableType' => $visitableType
        ]));

        return self::retrieve($id);
    }

    /**
     * @param string $identifier
     * @throws ModelNotFoundException
     * @return Visit
     */
    public static function retrieve(string $identifier): self
    {
        /** @var self $model */
        $model = self::query()->findOrFail($identifier);
        return $model;
    }
}
