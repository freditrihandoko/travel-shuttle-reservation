<?php

namespace App\Livewire;

use App\Models\Seat;
use App\Models\Vehicle;
use Livewire\Component;

class VehicleForm extends Component
{
    public $vehicleId;
    public $name;
    public $seat_count;
    public $seat_layout = [];
    public $rows = 3;
    public $cols = 3;
    public $seat_selected_count = 0;
    public $error_message;
    public $mode = 'seat';

    public function mount($vehicleId = null)
    {
        if ($vehicleId) {
            $vehicle = Vehicle::find($vehicleId);
            $this->vehicleId = $vehicle->id;
            $this->name = $vehicle->name;
            $this->seat_count = $vehicle->seat_count;
            $this->seat_layout = $vehicle->seat_layout;
            $this->rows = count($this->seat_layout);
            $this->cols = count($this->seat_layout[0]);
            $this->updateSeatSelectedCount();
        } else {
            $this->initializeSeatLayout();
        }
    }

    public function updatedRows()
    {
        $this->initializeSeatLayout();
    }

    public function updatedCols()
    {
        $this->initializeSeatLayout();
    }

    public function initializeSeatLayout()
    {
        $newLayout = [];
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $newLayout[$i][$j] = [
                    'seat_type' => isset($this->seat_layout[$i][$j]['seat_type']) ? $this->seat_layout[$i][$j]['seat_type'] : 'is_not_seat',
                    'seat_number' => isset($this->seat_layout[$i][$j]['seat_number']) ? $this->seat_layout[$i][$j]['seat_number'] : null,
                ];
            }
        }
        $this->seat_layout = $newLayout;
        $this->updateSeatSelectedCount();
    }

    public function toggleSeat($row, $col)
    {
        if ($this->mode === 'seat') {
            if ($this->seat_layout[$row][$col]['seat_type'] == 'is_seat') {
                $this->seat_layout[$row][$col]['seat_type'] = 'is_not_seat';
                $this->seat_layout[$row][$col]['seat_number'] = null;
            } else {
                if ($this->seat_selected_count >= $this->seat_count) {
                    $this->error_message = 'You cannot select more seats than the seat available.';
                    return;
                }
                $this->seat_layout[$row][$col]['seat_type'] = 'is_seat';
                $this->seat_layout[$row][$col]['seat_number'] = $this->seat_selected_count + 1;
            }
        } elseif ($this->mode === 'driver') {
            foreach ($this->seat_layout as &$rowLayout) {
                foreach ($rowLayout as &$seat) {
                    if ($seat['seat_type'] == 'driver') {
                        $seat['seat_type'] = 'is_not_seat';
                    }
                }
            }
            $this->seat_layout[$row][$col]['seat_type'] = 'driver';
            $this->seat_layout[$row][$col]['seat_number'] = null;
        }
        $this->updateSeatSelectedCount();
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
        $this->error_message = null;
    }

    public function updateSeatSelectedCount()
    {
        $this->seat_selected_count = 0;
        foreach ($this->seat_layout as &$rowLayout) {
            foreach ($rowLayout as &$seat) {
                if ($seat['seat_type'] == 'is_seat') {
                    $this->seat_selected_count++;
                    $seat['seat_number'] = $this->seat_selected_count;
                }
            }
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'seat_count' => 'required|integer',
            'seat_layout' => 'required|array',
        ]);

        if ($this->seat_selected_count > $this->seat_count) {
            $this->error_message = 'The number of selected seats exceeds the seat count.';
            return;
        }

        $vehicle = Vehicle::updateOrCreate(
            ['id' => $this->vehicleId],
            [
                'name' => $this->name,
                'seat_count' => $this->seat_count,
                'seat_layout' => $this->seat_layout,
            ]
        );

        // Delete all existing seats for the vehicle
        Seat::where('vehicle_id', $vehicle->id)->delete();

        foreach ($this->seat_layout as $i => $rowLayout) {
            foreach ($rowLayout as $j => $seat) {
                Seat::create([
                    'vehicle_id' => $vehicle->id,
                    'seat_number' => $seat['seat_number'],
                    'seat_type' => $seat['seat_type'],
                ]);
            }
        }

        return redirect()->route('vehicles.index')->with('success', 'Vehicle saved successfully.');
    }

    public function render()
    {
        return view('livewire.vehicle-form');
    }
}
