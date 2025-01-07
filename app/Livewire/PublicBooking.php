<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Pool;
use App\Models\Seat;
use App\Models\Trip;
use Livewire\Component;
use App\Models\Passenger;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PublicBooking extends Component
{
    public $cities;
    public $fromPools = [];
    public $toPools = [];
    public $selectedFromCity = null;
    public $selectedToCity = null;
    public $selectedDate;
    public $availableTrips = [];
    public $selectedTrip = null;
    public $name;
    public $email;
    public $phone;
    public $passengers = [['name' => '', 'seat_number' => null]];
    public $availableSeats = [];
    public $selectedSeats = [];
    public $totalAmount;
    public $showConfirmationModal = false;
    public $rows = 3;
    public $cols = 3;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:15',
        'passengers.*.name' => 'required|string|max:255',
        'selectedSeats' => 'required|array|max:3',
    ];

    protected $messages = [
        'name.required' => 'Nama Pemesan wajib diisi',
        'name.max' => 'Nama maksimal 255 karakter',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.max' => 'Email maksimal 255 karakter',
        'phone.required' => 'Nomor telepon wajib diisi',
        'phone.max' => 'Nomor telepon maksimal 15 karakter',
        'passengers.*.name.required' => 'Nama penumpang wajib diisi',
        'passengers.*.name.max' => 'Nama penumpang maksimal 255 karakter',
        'selectedSeats.required' => 'Pilih minimal 1 kursi',
        'selectedSeats.array' => 'Data kursi tidak valid',
        'selectedSeats.max' => 'Maksimal 3 kursi dapat dipilih',
    ];

    public function mount()
    {
        $this->cities = City::with('pools')->get();
    }

    public function updatedSelectedFromCity($cityId)
    {
        $this->fromPools = Pool::where('city_id', $cityId)->get();
        $this->selectedToCity = null;
        $this->toPools = [];
        $this->availableTrips = [];
        $this->selectedTrip = null;
        $this->selectedSeats = [];
    }

    public function updatedSelectedToCity($cityId)
    {
        $this->toPools = Pool::where('city_id', $cityId)->get();
        $this->availableTrips = [];
    }

    public function searchTrips()
    {
        $this->validate([
            'selectedFromCity' => 'required',
            'selectedToCity' => 'required',
            'selectedDate' => 'required|date|after_or_equal:today',
        ]);

        $today = now()->toDateString();
        $selectedDate = $this->selectedDate;

        $this->availableTrips = Trip::whereHas('route', function ($query) {
            $query->where('from_pool_id', $this->selectedFromCity)
                ->where('to_pool_id', $this->selectedToCity);
        })
            ->whereDate('departure_time', $selectedDate)
            ->when($selectedDate == $today, function ($query) {
                $query->whereTime('departure_time', '>', now());
            })
            ->with(['vehicle', 'route.fromPool.city', 'route.toPool.city'])
            ->get();
    }

    public function selectTrip($tripId)
    {
        $this->selectedTrip = Trip::with(['vehicle', 'route.fromPool.city', 'route.toPool.city', 'reservations.passengers'])
            ->findOrFail($tripId);

        $bookedSeats = $this->selectedTrip->reservations->flatMap(function ($reservation) {
            return $reservation->passengers->pluck('seat_id');
        });
        $this->availableSeats = $this->selectedTrip->vehicle->seats->map(function ($seat) use ($bookedSeats) {
            return (object) [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'seat_type' => $seat->seat_type,
                'reserved' => $bookedSeats->contains($seat->id)
            ];
        });

        $this->seat_layout = $this->selectedTrip->vehicle->seat_layout;
        $this->rows = count($this->seat_layout);
        $this->cols = count($this->seat_layout[0]);

        $this->totalAmount = 0;
        $this->selectedSeats = [];
        $this->passengers = [['name' => '', 'seat_number' => null]];
    }

    public function addPassenger()
    {
        if (count($this->passengers) < 3) {
            $this->passengers[] = ['name' => '', 'seat_number' => null];
        }
    }

    public function removePassenger($index)
    {
        if (count($this->passengers) > 1) {
            unset($this->passengers[$index]);
            $this->passengers = array_values($this->passengers);
            $this->calculateTotalAmount();
        }
    }

    public function selectSeat($seatId)
    {
        $seat = $this->availableSeats->firstWhere('id', $seatId);

        if ($seat) {
            $index = collect($this->passengers)->search(fn ($p) => $p['seat_number'] === $seatId);

            if ($index !== false) {
                $this->passengers[$index]['seat_number'] = null;
                $this->selectedSeats = array_diff($this->selectedSeats, [$seatId]);
            } else {
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
        $this->totalAmount = $this->selectedTrip ? $this->selectedTrip->price * count($this->passengers) : 0;
    }

    public function updateAvailableSeats()
    {
        $this->passengers = [['name' => '', 'seat_number' => null]];
        $this->selectedSeats = [];
        $bookedSeats = $this->selectedTrip->reservations->flatMap(function ($reservation) {
            return $reservation->passengers->pluck('seat_id');
        });

        $this->availableSeats = $this->selectedTrip->vehicle->seats->map(function ($seat) use ($bookedSeats) {
            return (object) [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'seat_type' => $seat->seat_type,
                'reserved' => $bookedSeats->contains($seat->id)
            ];
        });
    }


    public function openConfirmationModal()
    {
        $this->validate();

        $this->showConfirmationModal = true;
    }

    public function closeConfirmationModal()
    {
        $this->updateAvailableSeats();
        $this->showConfirmationModal = false;
    }

    public function generateReservationCode()
    {
        $prefix = 'TF';
        $date = now()->format('Ymd');
        $count = DB::table('reservations')
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1; // Incremental number for the day

        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public function confirmBooking()
    {
        sleep(3); //for testing only
        DB::transaction(function () {
            $this->validate();

            // Cek apakah kursi yang dipilih masih tersedia
            $selectedSeatIds = collect($this->passengers)->pluck('seat_number');
            $bookedSeats = $this->selectedTrip->reservations->flatMap(function ($reservation) {
                return $reservation->passengers->pluck('seat_id');
            });

            $conflictSeats = $selectedSeatIds->intersect($bookedSeats);


            if ($conflictSeats->isNotEmpty()) {
                // Ada kursi yang sudah dipesan, tampilkan pesan error dan perbarui kursi yang tersedia
                $this->showConfirmationModal = false;
                $this->updateAvailableSeats();
                $this->addError('selectedSeats', 'One or more seats have already been reserved. Please choose different seats.');
                return;
            }

            // Buat reservasi
            $reservation = Reservation::create([
                'trip_id' => $this->selectedTrip->id,
                // 'code' => Str::uuid(),
                'code' => $this->generateReservationCode(),
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
            $this->showConfirmationModal = false;
            return redirect()->route('check-reservation', ['code' => $reservation->code]);
        }, 5); // 5 adalah jumlah maksimal percobaan untuk menyelesaikan transaksi jika terjadi deadlock
    }

    public function render()
    {
        $filteredCities = $this->cities->filter(function ($city) {
            return $city->id != $this->selectedFromCity;
        });

        return view('livewire.public-booking', [
            'filteredCities' => $filteredCities,
        ])->layout('layouts.guest');
    }
}
