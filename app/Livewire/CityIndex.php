<?php

namespace App\Livewire;

use App\Models\City;
use Livewire\Component;
use Livewire\WithPagination;

class CityIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showCityModal = false;
    public $showDeleteModal = false;
    public $cityId = null;
    public $name = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showCreateModal()
    {
        $this->reset(['cityId', 'name']);
        $this->showCityModal = true;
    }

    public function showEditModal($cityId)
    {
        $city = City::findOrFail($cityId);
        $this->cityId = $city->id;
        $this->name = $city->name;
        $this->showCityModal = true;
        // $this->dispatch('open-modal', name: 'kota');
    }

    public function confirmDelete($cityId)
    {
        $this->cityId = $cityId;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->reset(['name', 'cityId']);
        $this->showCityModal = false;
    }

    public function saveCity()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . ($this->cityId ? $this->cityId : 'NULL'),
        ]);

        if ($this->cityId) {
            $city = City::findOrFail($this->cityId);
            $city->update(['name' => $this->name]);
        } else {
            City::create(['name' => $this->name]);
        }

        $this->reset(['cityId', 'name']);
        $this->showCityModal = false;
        $this->resetPage();
        session()->flash('success', 'City saved successfully.');
    }

    public function deleteCity()
    {
        // City::destroy($this->cityId);
        // $this->reset(['cityId', 'showDeleteModal']);
        // $this->resetPage();
        // session()->flash('success', 'City deleted successfully.');
        $city = City::find($this->cityId);

        if ($city && $city->pools()->exists()) {
            session()->flash('error', 'City cannot be deleted because it is associated with one or more pools.');
            $this->reset(['cityId', 'showDeleteModal']);
            return;
        }

        City::destroy($this->cityId);
        $this->reset(['cityId', 'showDeleteModal']);
        $this->resetPage();
        session()->flash('success', 'City deleted successfully.');
    }

    public function render()
    {
        $cities = City::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'ASC')
            ->paginate(10);

        return view('livewire.city-index', ['cities' => $cities]);
    }
}
