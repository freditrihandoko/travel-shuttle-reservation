<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\Route;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ReservationTrips extends Component
{
    use WithPagination;

    public $route_id;

    #[Url]
    public $search = '';

    #[Url]
    public $departure_date;

    public $sortField = 'departure_time';
    public $sortDirection = 'asc';

    protected $updatesQueryString = [
        'search' => ['except' => ''],
        'departure_date' => ['except' => ''],
    ];

    public function mount($routeId)
    {
        $this->route_id = $routeId;
        $this->departure_date = Carbon::now()->format('Y-m-d');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $query = Trip::where('route_id', $this->route_id)
            ->with(['vehicle', 'route.fromPool.city', 'route.toPool.city'])
            ->when($this->search, function ($query) {
                $query->whereHas('vehicle', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->departure_date, function ($query) {
                $query->whereDate('departure_time', $this->departure_date);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $trips = $query->paginate(10);

        $route = Route::findOrFail($this->route_id);

        return view('livewire.reservation-trips', [
            'trips' => $trips,
            'route' => $route,
        ]);
    }

    public function viewReservations($tripId)
    {
        return redirect()->route('reservations.list', ['tripId' => $tripId]);
    }

    public function viewSeats($tripId)
    {
        return redirect()->route('seat-layout', ['tripId' => $tripId]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDepartureDate()
    {
        $this->resetPage();
    }
}
