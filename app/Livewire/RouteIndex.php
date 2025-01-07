<?php

namespace App\Livewire;

use App\Models\Pool;
use App\Models\Route;
use Livewire\Component;
use Livewire\WithPagination;

class RouteIndex extends Component
{
    use WithPagination;

    public $pools;
    public $search = '';
    public $showRouteModal = false;
    public $showDeleteModal = false;
    public $routeId = null;
    public $from_pool_id = null;
    public $to_pool_id = null;

    protected $rules = [
        'from_pool_id' => 'required|exists:pools,id',
        'to_pool_id' => 'required|exists:pools,id|different:from_pool_id',
    ];

    protected $messages = [
        'to_pool_id.different' => 'The to pool must be different from the from pool.',
    ];

    public function mount()
    {
        $this->pools = Pool::with('city')->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function showCreateModal()
    {
        $this->reset(['routeId', 'from_pool_id', 'to_pool_id']);
        $this->showRouteModal = true;
    }

    public function showEditModal($routeId)
    {
        $route = Route::findOrFail($routeId);
        $this->routeId = $route->id;
        $this->from_pool_id = $route->from_pool_id;
        $this->to_pool_id = $route->to_pool_id;
        $this->showRouteModal = true;
    }

    public function confirmDelete($routeId)
    {
        $this->routeId = $routeId;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->reset(['routeId', 'from_pool_id', 'to_pool_id']);
        $this->showRouteModal = false;
    }

    public function saveRoute()
    {
        $this->validate();

        // Check if the route already exists
        $existingRoute = Route::where('from_pool_id', $this->from_pool_id)
            ->where('to_pool_id', $this->to_pool_id)
            ->first();

        if ($existingRoute && (!$this->routeId || $this->routeId != $existingRoute->id)) {
            session()->flash('error', 'This route already exists.');
            $this->closeModal();
            return;
        }

        if ($this->routeId) {
            $route = Route::findOrFail($this->routeId);
            if ($route->trips()->exists()) {
                session()->flash('error', 'Route cannot be updated because it is used in one or more trips.');
                $this->closeModal();
                return;
            }
            $route->update([
                'from_pool_id' => $this->from_pool_id,
                'to_pool_id' => $this->to_pool_id,
            ]);
        } else {
            Route::create([
                'from_pool_id' => $this->from_pool_id,
                'to_pool_id' => $this->to_pool_id,
            ]);
        }

        $this->reset(['routeId', 'from_pool_id', 'to_pool_id', 'showRouteModal']);
        $this->mount();
        session()->flash('success', 'Route saved successfully.');
    }

    public function deleteRoute()
    {
        $route = Route::findOrFail($this->routeId);
        if ($route->trips()->exists()) {
            session()->flash('error', 'Route cannot be deleted because it is used in one or more trips.');
            $this->reset(['routeId', 'showDeleteModal']);
            return;
        }
        $route->delete();

        $this->reset(['routeId', 'showDeleteModal']);
        $this->mount();
        session()->flash('success', 'Route deleted successfully.');
    }

    public function render()
    {
        $routes = Route::with(['fromPool.city', 'toPool.city'])
            ->whereHas('fromPool', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')->orWhereHas('city', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
            })->orWhereHas('toPool', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')->orWhereHas('city', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
            })->paginate(10);

        return view('livewire.route-index', [
            'routes' => $routes,
        ]);
    }
}
