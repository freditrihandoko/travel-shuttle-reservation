<?php

use App\Livewire\AdminDashboard;
use App\Livewire\TripView;
use App\Livewire\CityIndex;
use App\Livewire\PoolIndex;
use App\Livewire\TripIndex;
use App\Livewire\RouteIndex;
use App\Livewire\SeatLayout;
use App\Livewire\VehicleForm;
use App\Livewire\VehicleIndex;
use App\Livewire\PublicBooking;
use App\Livewire\ReservationList;
use App\Livewire\ReservationView;
use App\Livewire\CheckReservation;
use App\Livewire\ReservationIndex;
use App\Livewire\ReservationTrips;
use App\Livewire\CreateReservation;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/booking', PublicBooking::class)->name('booking');
Route::get('/check-reservation/{code?}', CheckReservation::class)->name('check-reservation');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

    Route::middleware(['role:super-admin|trip-admin'])->group(function () {
        Route::get('/vehicles', VehicleIndex::class)->name('vehicles.index');
        Route::get('/vehicles/create', VehicleForm::class)->name('vehicles.create');
        Route::get('/vehicles/edit/{vehicleId}', VehicleForm::class)->name('vehicles.edit');

        Route::get('cities', CityIndex::class)->name('cities.index');
        Route::get('pools', PoolIndex::class)->name('pools.index');
        Route::get('routes', RouteIndex::class)->name('routes.index');
    });

    // Trips routes accessible by super-admin and trip-admin
    Route::middleware(['role:super-admin|trip-admin'])->group(function () {
        Route::get('trips', TripIndex::class)->name('trips.index');
        Route::get('/trips/{routeId}', TripView::class)->name('trips.view');
    });

    Route::get('/trips/{tripId}/seats', SeatLayout::class)->name('seat-layout');

    // Reservations routes accessible by super-admin and reservation-admin
    Route::middleware(['role:super-admin|reservation-admin'])->group(function () {
        Route::get('/reservations/create/{tripId}', CreateReservation::class)->name('reservations.create');
        Route::get('reservations', ReservationIndex::class)->name('reservations.index');
        Route::get('/reservations/{routeId}', ReservationTrips::class)->name('reservations.trips');
        Route::get('/reservations/list/{tripId}', ReservationList::class)->name('reservations.list');
    });
});
