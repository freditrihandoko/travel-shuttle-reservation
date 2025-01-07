<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reservation;

class CheckReservation extends Component
{
    public $reservationCode;
    public $reservationDetails;

    public function mount($code = null)
    {
        if ($code) {
            $this->reservationCode = $code;
            $this->checkReservation();
        }
    }

    public function checkReservation()
    {
        $this->validate([
            'reservationCode' => 'required|string',
        ]);

        $this->reservationDetails = Reservation::where('code', $this->reservationCode)
            ->with('trip.route.fromPool.city', 'trip.route.toPool.city', 'passengers.seat')
            ->first();

        if (!$this->reservationDetails) {
            session()->flash('error', 'Reservation code not found.');
        }
    }

    public function maskEmail($email)
    {
        $parts = explode("@", $email);
        $name = implode('@', array_slice($parts, 0, count($parts) - 1));
        $len = strlen($name);
        return substr($name, 0, 1) . str_repeat('*', max(0, $len - 2)) . substr($name, -1) . "@" . end($parts);
    }

    public function maskPhone($phone)
    {
        return substr($phone, 0, 3) . str_repeat('*', strlen($phone) - 6) . substr($phone, -3);
    }

    public function render()
    {
        return view('livewire.check-reservation')->layout('layouts.guest');
    }
}
