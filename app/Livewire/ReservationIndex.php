<?php

namespace App\Livewire;

use App\Models\Route;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class ReservationIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRoute = null;

    #[Url]
    public $page = 1;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function selectRoute($routeId)
    {
        $this->selectedRoute = $routeId;
        return redirect()->route('reservations.trips', ['routeId' => $routeId]);
        $this->resetPage();
    }

    public function render()
    {
        $routes = Route::with(['fromPool.city', 'toPool.city'])
            ->whereHas('fromPool.city', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('toPool.city', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.reservation-index', [
            'routes' => $routes,
        ]);
    }
}
