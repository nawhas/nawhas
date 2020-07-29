<?php


namespace App\Modules\Popular\Models;


use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = ['ip', 'date', 'visitable_id', 'visitable_type'];
}
