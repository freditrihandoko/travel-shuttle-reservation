@php
    use Carbon\Carbon;
@endphp
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@endpush
<div>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('reservations.list', ['tripId' => $trip->id]) }}" class="mr-4">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-500"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"   d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create Reservation') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mt-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">
                        Selected Trip: {{ $trip->vehicle->name }} -
                        {{ $trip->route->fromPool->city->name }} - {{ $trip->route->fromPool->name }} to
                        {{ $trip->route->toPool->city->name }} - {{ $trip->route->toPool->name }} on
                        {{ Carbon::parse($trip->departure_time)->format('l, d F Y - H:i') }}
                    </h3>
                </div>
                @if (session()->has('error'))
                    <div class="bg-red-500 text-white p-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="mt-4">
                    <label for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
                    <input type="text" id="name" wire:model="name"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                    <label for="email"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mt-4">Email</label>
                    <input type="email" id="email" wire:model="email"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                    <label for="phone"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mt-4">Phone</label>
                    <input type="number" id="phone" wire:model="phone"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Passengers</label>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Seat
                                Layout</label>
                            <div
                                style="display: grid; grid-template-columns: repeat({{ $cols }}, minmax(0, 1fr)); gap: 8px;">
                                @foreach ($availableSeats as $seat)
                                    <div wire:click="selectSeat({{ $seat->id }})"
                                        class="p-4 border rounded cursor-pointer flex justify-center items-center flex-col
                                            {{ in_array($seat->id, $selectedSeats)
                                                ? 'bg-green-500'
                                                : ($seat->reserved
                                                    ? 'bg-red-500'
                                                    : ($seat->seat_type === 'driver'
                                                        ? 'bg-amber-500'
                                                        : ($seat->seat_type === 'is_not_seat'
                                                            ? 'bg-black'
                                                            : 'bg-gray-200'))) }}"
                                        {{ $seat->reserved || $seat->seat_type === 'driver' || $seat->seat_type === 'is_not_seat'
                                            ? 'style=pointer-events:none'
                                            : '' }}>
                                        @if ($seat->seat_type === 'driver')
                                            <i class="fas fa-user-tie text-white text-2xl"></i>
                                            <!-- Icon for driver -->
                                            <span class="text-white mt-2">Driver</span>
                                        @elseif ($seat->seat_type === 'is_not_seat')
                                            <i class="fas fa-times text-white text-2xl"></i>
                                            <!-- Icon for non-seat -->
                                            <span class="text-white mt-2">N/A</span>
                                        @else
                                            <i class="fas fa-user text-2xl"></i> <!-- Icon for seat -->
                                            <span class="text-black mt-2">Seat {{ $seat->seat_number }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Seats</h4>
                                <ul>
                                    @foreach ($selectedSeats as $selectedSeat)
                                        <li>
                                            <span
                                                class="whitespace-nowrap rounded-full bg-emerald-100 px-2.5 py-0.5 text-sm text-emerald-700 dark:bg-emerald-700 dark:text-emerald-100">
                                                {{ $availableSeats->firstWhere('id', $selectedSeat)->seat_number }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                                @error('selectedSeats')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror

                            </div>

                            <div class="mt-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200">Passengers</label>
                                @foreach ($passengers as $index => $passenger)
                                    <div class="flex items-center mt-2">
                                        <input type="text" wire:model="passengers.{{ $index }}.name"
                                            placeholder="Passenger Name"
                                            class="mr-2 rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        {{-- <span>Seat {{ $passenger['seat_number'] }}</span> --}}
                                        <button wire:click.prevent="removePassenger({{ $index }})"
                                            class="ml-2 text-red-500">Remove</button>
                                    </div>
                                @endforeach
                                <button wire:click.prevent="addPassenger" class="mt-2 text-blue-500">Add
                                    Passenger</button>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="mt-4">
                        <h5 class="text-lg font-medium text-red-500">Errors:</h5>
                        <ul class="list-disc text-red-500">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="mt-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">Total Amount:
                        {{ number_format($totalAmount, 0, ',', '.') }} IDR</h3>
                    <button wire:click.prevent="save" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Save
                        Reservation</button>
                </div>
            </div>
        </div>
        <div wire:loading wire:target="save" class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
            <div class="flex items-center justify-center min-h-screen">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
            </div>
        </div>
    </div>
</div>
