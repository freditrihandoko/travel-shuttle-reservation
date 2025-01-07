<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_id', 'route_id', 'departure_time', 'price'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }


    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
