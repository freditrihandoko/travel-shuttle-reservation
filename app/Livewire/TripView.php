<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\Route;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class TripView extends Component
{
    use WithPagination;

    public $route_id;
    public $route;
    public $vehicles;
    public $perPage = 10;
    public $vehicle_id, $price, $departure_times = [''];
    public $tripId;
    public $isModalOpen = false;
    public $isDeleteModalOpen = false;
    public $deleteTripId;
    public $sortField = 'departure_time';
    public $sortDirection = 'asc';

    #[Url]
    public $departure_date;

    #[Url]
    public $search = '';

    public function mount($routeId)
    {
        $this->route_id = $routeId;
        $this->route = Route::with(['fromPool.city', 'toPool.city'])->findOrFail($routeId);
        $this->vehicles = Vehicle::all();

        // Periksa parameter departure_date dari URL
        $departureDateFromUrl = request('departure_date');
        if ($departureDateFromUrl) {
            $this->departure_date = $departureDateFromUrl;
        } else {
            $this->departure_date = Carbon::now()->format('Y-m-d');
        }
    }

    public function addDepartureTime()
    {
        $this->departure_times[] = '';
    }

    public function removeDepartureTime($index)
    {
        unset($this->departure_times[$index]);
        $this->departure_times = array_values($this->departure_times);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDepartureDate()
    {
        $this->resetPage();
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

    public function openModal()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function openDeleteModal($id)
    {
        $this->deleteTripId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function closeDeleteModal()
    {
        $this->isDeleteModalOpen = false;
        $this->deleteTripId = null;
    }

    public function resetInputFields()
    {
        $this->vehicle_id = '';
        $this->price = '';
        $this->departure_times = [];
        $this->tripId = '';
    }

    public function store()
    {
        $this->validate([
            'vehicle_id' => 'required',
            'departure_times.*' => 'required',
            'price' => 'required|numeric',
        ]);

        if ($this->tripId) {
            // Update the existing trip
            $trip = Trip::findOrFail($this->tripId);

            // Check if the departure time and vehicle combination already exists
            if (Trip::where('vehicle_id', $this->vehicle_id)
                ->where('departure_time', $this->departure_times[0]) // Assuming only one time for edit
                ->where('id', '!=', $this->tripId) // Exclude the current trip from the check
                ->exists()
            ) {
                session()->flash('error', 'Trip with the same vehicle and departure time already exists.');
                $this->closeModal();
                return;
            }

            // Update the trip
            $trip->update([
                'vehicle_id' => $this->vehicle_id,
                'route_id' => $this->route_id,
                'departure_time' => $this->departure_times[0], // Assuming only one time for edit
                'price' => $this->price,
            ]);

            session()->flash('success', 'Trip updated successfully.');
        } else {
            // Create new trips for each departure time
            foreach ($this->departure_times as $departure_time) {
                if (Trip::where('vehicle_id', $this->vehicle_id)
                    ->where('departure_time', $departure_time)
                    ->exists()
                ) {
                    session()->flash('error', 'Trip with the same vehicle and departure time already exists.');
                    $this->closeModal();
                    return;
                }

                Trip::create([
                    'vehicle_id' => $this->vehicle_id,
                    'route_id' => $this->route_id,
                    'departure_time' => $departure_time,
                    'price' => $this->price,
                ]);
            }

            session()->flash('success', 'Trip created successfully.');
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $trip = Trip::findOrFail($id);

        $this->vehicle_id = $trip->vehicle_id;
        $this->departure_times = [$trip->departure_time];
        $this->price = $trip->price;

        $this->tripId = $id;
        $this->isModalOpen = true;
    }

    public function delete()
    {
        $trip = Trip::findOrFail($this->deleteTripId);

        if (!$trip->reservations()->exists()) {
            $trip->delete();
            session()->flash('success', 'Trip deleted successfully.');
        } else {
            session()->flash('error', 'Cannot delete trip with existing reservations.');
        }

        $this->closeDeleteModal();
    }

    public function render()
    {
        $trips = Trip::with(['vehicle', 'route.fromPool.city', 'route.toPool.city'])
            ->where('route_id', $this->route_id)
            ->when($this->search, function ($query) {
                $query->whereHas('vehicle', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('route.fromPool.city', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('route.toPool.city', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->departure_date, function ($query) {
                $query->whereDate('departure_time', $this->departure_date);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.trip-view', [
            'trips' => $trips,
        ]);
    }
}
