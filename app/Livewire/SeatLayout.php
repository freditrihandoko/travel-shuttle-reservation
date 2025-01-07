<?php

namespace App\Livewire;

use App\Models\Trip;
use Livewire\Component;

class SeatLayout extends Component
{
    public $tripId;
    public $trip;
    public $rows = 3;
    public $cols = 3;

    public function mount($tripId)
    {

        $this->tripId = $tripId;
        $this->trip = Trip::with(['vehicle', 'route.fromPool.city', 'route.toPool.city', 'reservations.passengers.seat'])->findOrFail($tripId);
        $this->seat_layout = $this->trip->vehicle->seat_layout;
        $this->rows = count($this->seat_layout);
        $this->cols = count($this->seat_layout[0]);
    }

    public function render()
    {
        return view('livewire.seat-layout', [
            'trip' => $this->trip,
        ]);
    }
}
