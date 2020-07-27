<?php


namespace App\Modules\Library\Models;


use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    use HasUuid;
    use UsesDataConnection;

    protected $fillable = ['id', 'entity', 'entity_id', 'date'];

    public function reciters(): HasOne
    {
        return $this->hasOne(Reciter::class, 'id', 'entity_id');
    }

    public function tracks(): HasOne
    {
        return $this->hasOne(Track::class, 'id', 'entity_id');
    }
}
