<?php

namespace App\Livewire;

use App\Models\Trip;
use App\Models\Route;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class TripIndex extends Component
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
        // $this->selectedRoute = $routeId;
        // $this->dispatch('routeSelected', $routeId);
        // $this->resetPage();

        $this->selectedRoute = $routeId;
        return redirect()->route('trips.view', ['routeId' => $routeId]);
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

        return view('livewire.trip-index', [
            'routes' => $routes,
        ]);
    }
}
