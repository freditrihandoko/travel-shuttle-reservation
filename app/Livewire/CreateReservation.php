<?php

namespace App\Livewire;

use App\Models\Trip;
use Livewire\Component;
use App\Models\Passenger;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateReservation extends Component
{
    public $trip;
    public $name;
    public $email;
    public $phone;
    public $passengers = [];
    public $selectedSeats = [];
    public $availableSeats = [];
    public $totalAmount = 0;
    public $rows = 3;
    public $cols = 3;

    public function mount($tripId)
    {
        $this->trip = Trip::with('vehicle.seats', 'reservations.passengers')->find($tripId);
        $bookedSeats = $this->trip->reservations->flatMap(function ($reservation) {
            return $reservation->passengers->pluck('seat_id');
        });

        $this->availableSeats = $this->trip->vehicle->seats->map(function ($seat) use ($bookedSeats) {
            return (object) [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'seat_type' => $seat->seat_type,
                'reserved' => $bookedSeats->contains($seat->id)
            ];
        });

        $this->seat_layout = $this->trip->vehicle->seat_layout;
        $this->rows = count($this->seat_layout);
        $this->cols = count($this->seat_layout[0]);

        $this->reset(['passengers', 'selectedSeats', 'totalAmount']);
    }

    public function addPassenger()
    {
        $this->passengers[] = ['name' => '', 'seat_number' => null];
        $this->calculateTotalAmount();
    }

    public function removePassenger($index)
    {
        unset($this->passengers[$index]);
        $this->passengers = array_values($this->passengers);
        $this->calculateTotalAmount();
    }

    public function selectSeat($seatId)
    {
        $seat = $this->availableSeats->firstWhere('id', $seatId);

        if ($seat) {
            // Check if the seat is already selected
            $index = collect($this->passengers)->search(fn ($p) => $p['seat_number'] === $seatId);

            if ($index !== false) {
                // Deselect the seat
                $this->passengers[$index]['seat_number'] = null;
                $this->selectedSeats = array_diff($this->selectedSeats, [$seatId]);
            } else {
                // Select the seat if there's an empty passenger slot
                $index = collect($this->passengers)->search(fn ($p) => $p['seat_number'] === null);
                if ($index !== false) {
                    $this->passengers[$index]['seat_number'] = $seatId;
                    $this->selectedSeats[] = $seatId;
                }
            }
        }

        $this->calculateTotalAmount();
    }

    public function calculateTotalAmount()
    {
        $this->totalAmount = $this->trip ? $this->trip->price * count($this->passengers) : 0;
    }

    public function updateAvailableSeats()
    {
        // Reset the passengers array and selected seats
        $this->passengers = [['name' => '', 'seat_number' => null]];
        $this->selectedSeats = [];

        // Get all the booked seats for the selected trip
        $bookedSeats = $this->trip->reservations->flatMap(function ($reservation) {
            return $reservation->passengers->pluck('seat_id');
        });

        // Map the available seats, marking the ones that are already reserved
        $this->availableSeats = $this->trip->vehicle->seats->map(function ($seat) use ($bookedSeats) {
            return (object) [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'seat_type' => $seat->seat_type,
                'reserved' => $bookedSeats->contains($seat->id)
            ];
        });
    }


    public function save()
    {
        DB::transaction(function () {
            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'passengers' => 'required|array|min:1',
                'passengers.*.name' => 'required|string|max:255',
                'passengers.*.seat_number' => 'required|integer|distinct'
            ], [
                'passengers.*.seat_number.distinct' => 'Seat number must be unique.'
            ]);

            // Check if the selected seats are still available
            $selectedSeatIds = collect($this->passengers)->pluck('seat_number');
            $bookedSeats = $this->trip->reservations->flatMap(function ($reservation) {
                return $reservation->passengers->pluck('seat_id');
            });

            $conflictSeats = $selectedSeatIds->intersect($bookedSeats);

            if ($conflictSeats->isNotEmpty()) {
                // Some seats are already booked, show error message
                $this->updateAvailableSeats();
                $this->addError('selectedSeats', 'One or more seats have already been reserved. Please choose different seats.');
                return;
            }

            // Create the reservation
            $reservation = Reservation::create([
                'trip_id' => $this->trip->id,
                'code' => Str::uuid(),
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'status' => 'pending',
                'total_amount' => $this->totalAmount
            ]);

            foreach ($this->passengers as $passenger) {
                Passenger::create([
                    'reservation_id' => $reservation->id,
                    'seat_id' => $passenger['seat_number'],
                    'name' => $passenger['name']
                ]);
            }

            session()->flash('success', 'Reservation created successfully.');
            return redirect()->route('reservations.list', ['tripId' => $this->trip->id]);
        }, 5); // Retry the transaction up to 5 times in case of a deadlock
    }

    public function render()
    {
        return view('livewire.create-reservation');
    }
}
