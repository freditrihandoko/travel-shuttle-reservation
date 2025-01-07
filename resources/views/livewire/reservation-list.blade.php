@php
    use Carbon\Carbon;
@endphp
<div>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('reservations.trips', ['routeId' => $trip->route->id]) }}" class="mr-4">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-500"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"   d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Reservations for Trip: {{ $trip->vehicle->name }} -
                {{ Carbon::parse($trip->departure_time)->format('l, d F Y - H:i') }}
                <br>
                Route: {{ $trip->route->fromPool->city->name }} - {{ $trip->route->fromPool->name }} to
                {{ $trip->route->toPool->city->name }} - {{ $trip->route->toPool->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div
                    class="p-6 overflow-x-auto text-center bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

                    <div class="mb-4 flex justify-between">
                        <input type="text" wire:model.live="search" placeholder="Search..."
                            class="form-input w-full border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        @php
                            $reservedSeats = $trip->reservations
                                ->flatMap(fn($reservation) => $reservation->passengers)
                                ->count();

                            $now = now();
                            $departureTime = Carbon::parse($trip->departure_time);
                            $timeAfterDeparture = $departureTime->copy()->addMinutes(15);
                        @endphp

                        @if ($now > $timeAfterDeparture)
                            <span
                                class="ml-4 inline-block rounded bg-gray-600 px-4 py-2 text-xs font-medium text-white">
                                Time Passed
                            </span>
                        @elseif ($reservedSeats < $trip->vehicle->seat_count)
                            @if ($now <= $departureTime)
                                <a href="{{ route('reservations.create', $trip->id) }}"
                                    class="ml-4 inline-block rounded bg-green-600 px-4 py-2 text-xs font-medium text-white hover:bg-green-700">
                                    Create Reservation
                                </a>
                            @elseif ($now <= $timeAfterDeparture)
                                <a href="{{ route('reservations.create', $trip->id) }}"
                                    class="ml-4 inline-block rounded bg-yellow-500 px-4 py-2 text-xs font-medium text-white hover:bg-yellow-600">
                                    Create Reservation (time up)
                                </a>
                            @endif
                        @else
                            <span class="ml-4 inline-block rounded bg-red-600 px-4 py-2 text-xs font-medium text-white">
                                Fully Booked
                            </span>
                        @endif
                    </div>

                    @if (session()->has('success'))
                        <div class="bg-green-500 text-white p-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-500 text-white p-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <table
                        class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm dark:divide-gray-700 dark:bg-gray-900 rounded-lg overflow-hidden">
                        <thead class="ltr:text-left rtl:text-right">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">No
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white cursor-pointer"
                                    wire:click="sortBy('name')">
                                    Name
                                    @if ($sortField == 'name')
                                        <span>{!! $sortDirection == 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                                    @endif
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    Passengers</th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Seats
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white cursor-pointer"
                                    wire:click="sortBy('status')">
                                    Status
                                    @if ($sortField == 'status')
                                        <span>{!! $sortDirection == 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                                    @endif
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white cursor-pointer"
                                    wire:click="sortBy('created_at')">
                                    Booking At
                                    @if ($sortField == 'created_at')
                                        <span>{!! $sortDirection == 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                                    @endif
                                </th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($reservations as $reservation)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                        {{ $reservations->firstItem() + $loop->index }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $reservation->name }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        @if ($reservation->passengers && $reservation->passengers->isNotEmpty())
                                            @foreach ($reservation->passengers as $passenger)
                                                {{ $passenger->name }} - {{ $passenger->seat->seat_number }}<br>
                                            @endforeach
                                        @else
                                            No passengers
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                        @php
                                            $reservedSeats = $reservation->passengers->count();
                                            $totalSeats = $reservation->trip->vehicle->seat_count;
                                        @endphp
                                        {{ $reservedSeats }}/{{ $totalSeats }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $reservation->status }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ Carbon::parse($reservation->created_at)->format('l, d F Y - H:i') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        <button wire:click="openModal({{ $reservation->id }})"
                                            class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                            View
                                        </button>
                                        @if ($reservation->status === 'pending')
                                            <button wire:click="openConfirmModal({{ $reservation->id }})"
                                                class="inline-block rounded bg-green-600 px-4 py-2 text-xs font-medium text-white hover:bg-green-700">
                                                Confirm
                                            </button>
                                            <button wire:click="openCancelModal({{ $reservation->id }})"
                                                class="inline-block rounded bg-red-600 px-4 py-2 text-xs font-medium text-white hover:bg-red-700">
                                                Cancel
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($reservations->isEmpty())
                        <p class="text-center py-4 text-gray-600 dark:text-gray-400">
                            No Reservation found.<br>
                        </p>
                    @endif

                    <div class="mt-4">
                        {{ $reservations->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{-- All Modal --}}
    <div>
        @if ($isModalOpen)
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-xl w-full max-w-lg overflow-auto">
                    <div class="flex justify-end">
                        <button wire:click="closeModal"
                            class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight mb-4">
                            Reservation Details</h2>
                        @if ($selectedReservation)
                            <p class="text-gray-800 dark:text-gray-200 "><strong>Reservation Code:</strong>
                                {{ $selectedReservation->code }}</p>
                            <p class="text-gray-800 dark:text-gray-200 "><strong>Name:</strong>
                                {{ $selectedReservation->name }}</p>
                            <p class="text-gray-800 dark:text-gray-200 "><strong>Email:</strong>
                                {{ $selectedReservation->email }}</p>
                            <p class="text-gray-800 dark:text-gray-200 "><strong>Phone:</strong>
                                {{ $selectedReservation->phone }}</p>
                            <p class="text-gray-800 dark:text-gray-200 "><strong>Trip:</strong>
                                {{ $selectedReservation->trip->route->fromPool->city->name }} to
                                {{ $selectedReservation->trip->route->toPool->city->name }} -
                                {{ Carbon::parse($selectedReservation->trip->departure_time)->format('l, d F Y - H:i') }}
                            </p>
                            <p class="text-gray-800 dark:text-gray-200 "><strong>Status:</strong>
                                {{ ucfirst($selectedReservation->status) }}</p>
                            <h3 class="font-semibold mt-4 text-gray-800 dark:text-gray-200 ">Passengers:
                            </h3>
                            <ul class="text-gray-800 dark:text-gray-200 ">
                                @foreach ($selectedReservation->passengers as $passenger)
                                    <li>{{ $passenger->name }} - Seat:
                                        {{ $passenger->seat->seat_number }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($isConfirmModalOpen)
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div
                    class="bg-white dark:bg-gray-800 p-4  text-gray-800 dark:text-gray-200 rounded-lg shadow-xl w-full max-w-lg overflow-auto">
                    <div class="flex justify-end">
                        <button wire:click="closeConfirmModal"
                            class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight mb-4">
                            Confirm Reservation</h2>
                        <p>Are you sure you want to confirm this reservation?</p>
                        <div class="flex justify-end space-x-2 mt-4">
                            <button wire:click="confirmReservation"
                                class="bg-green-600 px-4 py-2 text-white rounded-md hover:bg-green-700">Confirm</button>
                            <button wire:click="closeConfirmModal"
                                class="bg-gray-600 px-4 py-2 text-white rounded-md hover:bg-gray-700">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($isCancelModalOpen)
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div
                    class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-4 rounded-lg shadow-xl w-full max-w-lg overflow-auto">
                    <div class="flex justify-end">
                        <button wire:click="closeCancelModal"
                            class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight mb-4">
                            Cancel Reservation</h2>
                        <p>Are you sure you want to cancel this reservation?</p>
                        <div class="flex justify-end space-x-2 mt-4">
                            <button wire:click="cancelReservation"
                                class="bg-red-600 px-4 py-2 text-white rounded-md hover:bg-red-700">Cancel
                                Reservation</button>
                            <button wire:click="closeCancelModal"
                                class="bg-gray-600 px-4 py-2 text-white rounded-md hover:bg-gray-700">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div wire:loading wire:target="confirmReservation"
            class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
            <div class="flex items-center justify-center min-h-screen">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
            </div>
        </div>
        <div wire:loading wire:target="cancelReservation"
            class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
            <div class="flex items-center justify-center min-h-screen">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
            </div>
        </div>

    </div>
</div>
