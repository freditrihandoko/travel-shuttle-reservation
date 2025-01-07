<?php

namespace App\Livewire;

use App\Models\Trip;
use Livewire\Component;
use App\Models\Reservation;
use Livewire\WithPagination;

class ReservationList extends Component
{
    use WithPagination;

    public $trip_id;
    public $reservationId;
    public $date;
    public $status;
    public $search = '';
    public $isModalOpen = false;
    public $isConfirmModalOpen = false;
    public $isCancelModalOpen = false;
    public $isCreateModalOpen = false;
    public $showCreateModal = false;
    public $selectedTripId;
    public $name, $email, $phone, $passengers, $total_amount, $selectedReservation;

    public $selectedTrip;

    public $sortField = 'created_at';
    public $sortDirection = 'asc';

    protected $listeners = [
        'closeCreateModal' => 'closeCreateModal'
    ];

    public function mount($tripId)
    {
        $this->trip_id = $tripId;
    }

    public function render()
    {
        $query = Reservation::where('trip_id', $this->trip_id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $reservations = $query->paginate(10);

        $trip = Trip::with(['vehicle', 'route.fromPool.city', 'route.toPool.city'])->findOrFail($this->trip_id);

        return view('livewire.reservation-list', [
            'reservations' => $reservations,
            'trip' => $trip,
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openModal($id)
    {
        $this->selectedReservation = Reservation::with('passengers.seat')->findOrFail($id);
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    public function openConfirmModal($id)
    {
        $this->reservationId = $id;
        $this->isConfirmModalOpen = true;
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
        $this->resetInputFields();
    }

    public function openCancelModal($id)
    {
        $this->reservationId = $id;
        $this->isCancelModalOpen = true;
    }

    public function closeCancelModal()
    {
        $this->isCancelModalOpen = false;
        $this->resetInputFields();
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    private function resetInputFields()
    {
        $this->status = '';
        $this->reservationId = '';
        $this->selectedReservation = null;
    }

    public function confirmReservation()
    {
        $reservation = Reservation::findOrFail($this->reservationId);
        $reservation->update(['status' => 'confirmed']);
        session()->flash('success', 'Reservation confirmed successfully.');
        $this->closeConfirmModal();
    }

    public function cancelReservation()
    {
        $reservation = Reservation::findOrFail($this->reservationId);
        $reservation->update(['status' => 'cancelled']);
        $reservation->passengers()->delete();
        session()->flash('success', 'Reservation cancelled successfully.');
        $this->closeCancelModal();
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
}
