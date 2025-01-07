<?php

namespace App\Livewire;

use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleIndex extends Component
{

    use WithPagination;

    public $search = '';
    public $showVehicleModal = false;
    public $showDeleteModal = false;
    public $selectedVehicle = null;

    public $seat_layout = [];
    public $rows = 3;
    public $cols = 3;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showVehicle($vehicleId)
    {
        $this->selectedVehicle = Vehicle::findOrFail($vehicleId);
        $this->seat_layout = $this->selectedVehicle->seat_layout;
        $this->rows = count($this->seat_layout);
        $this->cols = count($this->seat_layout[0]);

        $this->showVehicleModal = true;
    }

    public function confirmDelete($vehicleId)
    {
        $this->selectedVehicle = Vehicle::findOrFail($vehicleId);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->selectedVehicle && !$this->selectedVehicle->trips()->exists()) {
            $this->selectedVehicle->delete();
            session()->flash('success', 'Vehicle deleted successfully.');
        } else {
            session()->flash('error', 'Vehicle cannot be deleted because it is used in one or more trips.');
        }

        $this->showDeleteModal = false;
        $this->selectedVehicle = null;
    }

    public function render()
    {
        $vehicles = Vehicle::where('name', 'like', '%' . $this->search . '%')
            ->with('trips')
            ->paginate(10);

        return view('livewire.vehicle-index', ['vehicles' => $vehicles]);
    }
}
