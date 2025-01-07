<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = ['from_pool_id', 'to_pool_id'];

    public function fromPool()
    {
        return $this->belongsTo(Pool::class, 'from_pool_id');
    }

    public function toPool()
    {
        return $this->belongsTo(Pool::class, 'to_pool_id');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
