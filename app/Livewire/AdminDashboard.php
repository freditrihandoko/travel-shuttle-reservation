<?php

namespace App\Livewire;

use App\Models\Trip;
use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Reservation;

class AdminDashboard extends Component
{
    public $todayReservations;
    public $totalVehicles;
    public $activeTrips;
    public $pendingReservations;

    public function __construct()
    {
        $this->todayReservations = Reservation::whereDate('created_at', today())->count();
        $this->totalVehicles = Vehicle::count();
        $this->activeTrips = Trip::whereDate('departure_time', '>=', today())->count();
        $this->pendingReservations = Reservation::where('status', 'pending')->count();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
