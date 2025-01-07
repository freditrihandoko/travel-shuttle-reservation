<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Pool;
use App\Models\Route;
use Livewire\Component;
use Livewire\WithPagination;

class PoolIndex extends Component
{
    use WithPagination;

    public $cities;
    public $search = '';
    public $showPoolModal = false;
    public $showDeleteModal = false;
    public $poolId = null;
    public $city_id = null;
    public $name = '';
    public $address = '';

    protected function rules()
    {
        return [
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255|unique:pools,name,' . $this->poolId,
            'address' => 'required|string',
        ];
    }

    protected $messages = [
        'name.unique' => 'The pool name has already been taken.',
    ];

    public function mount()
    {
        $this->cities = City::all();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function showCreateModal()
    {
        $this->reset(['poolId', 'city_id', 'name', 'address']);
        $this->showPoolModal = true;
    }

    public function showEditModal($poolId)
    {
        $pool = Pool::findOrFail($poolId);
        $this->poolId = $pool->id;
        $this->city_id = $pool->city_id;
        $this->name = $pool->name;
        $this->address = $pool->address;
        $this->showPoolModal = true;
    }

    public function confirmDelete($poolId)
    {
        $this->poolId = $poolId;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->reset(['poolId', 'city_id', 'name', 'address']);
        $this->showPoolModal = false;
    }

    public function savePool()
    {
        $this->validate();

        if ($this->poolId) {
            $pool = Pool::findOrFail($this->poolId);
            $pool->update([
                'city_id' => $this->city_id,
                'name' => $this->name,
                'address' => $this->address,
            ]);
        } else {
            Pool::create([
                'city_id' => $this->city_id,
                'name' => $this->name,
                'address' => $this->address,
            ]);
        }

        $this->reset(['poolId', 'city_id', 'name', 'address', 'showPoolModal']);
        session()->flash('success', 'Pool saved successfully.');
        $this->resetPage();
    }

    public function deletePool()
    {
        $pool = Pool::find($this->poolId);

        if ($pool && (Route::where('from_pool_id', $this->poolId)->exists() || Route::where('to_pool_id', $this->poolId)->exists())) {
            session()->flash('error', 'Pool cannot be deleted because it is associated with one or more routes.');
            $this->reset(['poolId', 'showDeleteModal']);
            return;
        }

        Pool::destroy($this->poolId);
        $this->reset(['poolId', 'showDeleteModal']);
        session()->flash('success', 'Pool deleted successfully.');
        $this->resetPage();
    }

    public function render()
    {
        $pools = Pool::where('name', 'like', '%' . $this->search . '%')
            ->orWhereHas('city', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.pool-index', ['pools' => $pools]);
    }
}
