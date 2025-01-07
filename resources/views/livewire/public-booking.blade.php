<!-- resources/views/livewire/public-booking.blade.php -->
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="container mx-auto py-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">Book Your Trip</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="from_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
                    <select wire:model.live="selectedFromCity" id="from_city"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Select City</option>
                        @foreach ($cities as $city)
                            <optgroup label="{{ $city->name }}">
                                @foreach ($city->pools as $pool)
                                    <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="to_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
                    <select wire:model.live="selectedToCity" id="to_city"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Select City</option>
                        @foreach ($filteredCities as $city)
                            <optgroup label="{{ $city->name }}">
                                @foreach ($city->pools as $pool)
                                    <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="date"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                    <input type="date" wire:model.live="selectedDate" id="date"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                    @error('selectedDate')
                        <span class="text-red-600">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-span-1 md:col-span-2 flex justify-end">
                    @if (!$selectedTrip)
                        <button wire:click="searchTrips"
                            class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Search</button>
                    @endif
                </div>
                <div wire:loading wire:target="searchTrips"
                    class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
                    <div class="flex items-center justify-center min-h-screen">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
                    </div>
                </div>
            </div>
        </div>

        @if ($selectedTrip)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg mt-8">
                <h3 class="text-xl font-semibold mb-4">Complete Your Booking</h3>
                <p class="mb-4">Trip Details:</p>
                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-md mb-4">
                    <h4 class="text-lg font-semibold">{{ $selectedTrip->vehicle->name }}</h4>
                    <p>{{ $selectedTrip->route->fromPool->city->name }} - {{ $selectedTrip->route->fromPool->name }} to
                        {{ $selectedTrip->route->toPool->city->name }} - {{ $selectedTrip->route->toPool->name }}</p>
                    <p>{{ \Carbon\Carbon::parse($selectedTrip->departure_time)->format('l, d F Y - H:i') }}</p>
                    <p>Price per seat: Rp. {{ number_format($selectedTrip->price, 2) }}</p>
                </div>
                <form wire:submit.prevent="openConfirmationModal">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" wire:model.live="name" id="name"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                            @error('name')
                                <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" wire:model.live="email" id="email"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                            @error('email')
                                <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="phone"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <input type="number" wire:model.live="phone" id="phone"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                            @error('phone')
                                <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Seats</h4>
                        <div class="grid grid-cols-{{ $cols }} grid-rows-{{ $rows }} gap-4">
                            @foreach ($availableSeats as $seat)
                                @if ($seat->reserved)
                                    <button type="button" disabled
                                        class="bg-gray-400 text-white rounded-md p-2">{{ $seat->seat_number }}</button>
                                @elseif ($seat->seat_type === 'driver')
                                    <button type="button" disabled
                                        class="bg-gray-400 text-white rounded-md p-2">Driver</button>
                                @elseif ($seat->seat_type === 'is_not_seat')
                                    <button type="button" disabled
                                        class="bg-gray-400 text-white rounded-md p-2">X</button>
                                @else
                                    <button type="button"
                                        class="bg-blue-600 hover:bg-blue-700 text-white rounded-md p-2"
                                        wire:click="selectSeat({{ $seat->id }})">{{ $seat->seat_number }}</button>
                                @endif
                            @endforeach
                        </div>
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
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Passengers</h4>
                        @foreach ($passengers as $index => $passenger)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="passenger-name-{{ $index }}"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Passenger
                                        Name</label>
                                    <input type="text" wire:model.live="passengers.{{ $index }}.name"
                                        id="passenger-name-{{ $index }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                    @error('passengers.' . $index . '.name')
                                        <span class="text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <input type="hidden" wire:model.live="passengers.{{ $index }}.seat_number"
                                        id="passenger-seat-{{ $index }}">
                                </div>
                                @if (count($passengers) > 1)
                                    <div class="col-span-1 md:col-span-2">
                                        <button type="button" wire:click="removePassenger({{ $index }})"
                                            class="mt-2 inline-block bg-red-600 text-white px-4 py-2 rounded-md">Remove
                                            Passenger</button>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @if (count($passengers) < 3)
                            <button type="button" wire:click="addPassenger"
                                class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Add
                                More Passenger</button>
                        @endif
                    </div>

                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Amount:
                            Rp. {{ number_format($totalAmount, 2) }}</h4>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">Confirm
                            Booking</button>
                    </div>
                </form>

                <!-- Confirmation Modal -->
                <div wire:loading.remove wire:target="confirmBooking"
                    class="{{ $showConfirmationModal ? '' : 'hidden' }} fixed z-10 inset-0 overflow-y-auto">
                    <div
                        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            Confirm Booking</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Are you sure you want to confirm this
                                                booking?</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" wire:click="confirmBooking"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Confirm
                                </button>
                                <button type="button" wire:click="closeConfirmationModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div wire:loading wire:target="confirmBooking"
                    class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
                    <div class="flex items-center justify-center min-h-screen">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
                    </div>
                </div>

            </div>
        @else
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg mt-8">
                <h3 class="text-xl font-semibold mb-4">Available Trips</h3>
                <div class="grid grid-cols-1 gap-4">
                    @foreach ($availableTrips as $trip)
                        <div
                            class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-md flex justify-between items-center">
                            <div>
                                <h4 class="text-lg font-semibold">{{ $trip->vehicle->name }}</h4>
                                <p>{{ $trip->route->fromPool->city->name }} - {{ $trip->route->fromPool->name }} to
                                    {{ $trip->route->toPool->city->name }} - {{ $trip->route->toPool->name }}</p>
                                <p>{{ \Carbon\Carbon::parse($trip->departure_time)->format('l, d F Y - H:i') }}</p>
                                <p>Price per seat: Rp. {{ number_format($trip->price, 2) }}</p>
                                <p>Available Seats:
                                    @php
                                        $reservedSeats = $trip->reservations
                                            ->flatMap(fn($reservation) => $reservation->passengers)
                                            ->count();
                                    @endphp
                                    {{ $reservedSeats }} /
                                    {{ $trip->vehicle->seat_count }}
                                </p>
                            </div>
                            @php
                                $reservedSeats = $trip->reservations
                                    ->flatMap(fn($reservation) => $reservation->passengers)
                                    ->count();
                                $now = now();
                                $departureTime = Carbon\Carbon::parse($trip->departure_time);
                                $diffInMinutes = $now->diffInMinutes($departureTime);
                            @endphp
                            {{-- @dump($diffInMinutes) --}}
                            @if ($diffInMinutes < 120)
                                <button disabled class="bg-gray-400 text-white px-4 py-2 rounded-md">Call to
                                    Book</button>
                            @elseif ($reservedSeats < $trip->vehicle->seat_count)
                                <button wire:click="selectTrip({{ $trip->id }})"
                                    class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">Book
                                    Now</button>
                            @else
                                <span class="inline-block bg-red-600 text-white px-4 py-2 rounded-md">Fully
                                    Booked</span>
                            @endif
                        </div>
                        <div wire:loading wire:target="selectTrip"
                            class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
                            <div class="flex items-center justify-center min-h-screen">
                                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if (empty($availableTrips) || count($availableTrips) === 0)
                        <p class="text-center py-4 text-gray-600 dark:text-gray-400">
                            No trip found.<br>
                            Please change the date selection!
                        </p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
