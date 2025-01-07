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
                    <a href="{{ route('reservations.list', ['tripId' => $trip->id]) }}"
                        class="inline-block rounded bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700">Make
                        Reservation</a>
                    <h3 class="text-xl font-semibold mt-6 mb-2 text-gray-800 dark:text-gray-200">Seat Layout</h3>
                    <div
                        class="grid grid-cols-{{ $cols }} grid-rows-{{ $rows }} gap-4 bg-white dark:bg-gray-800">
                        @foreach ($trip->vehicle->seats as $seat)
                            <div class="border rounded p-2 ">
                                <p class="font-bold text-gray-900 dark:text-white">{{ $seat->seat_number }}</p>
                                @php
                                    $reservation = $trip->reservations->flatMap->passengers->firstWhere(
                                        'seat_id',
                                        $seat->id,
                                    );
                                @endphp
                                @if ($reservation)
                                    <button type="button"
                                        class="bg-green-600 dark:bg-green-500 text-white rounded-md p-2">
                                        {{ $reservation->name }}
                                    </button>
                                @elseif ($seat->seat_type === 'driver')
                                    <button type="button" disabled
                                        class="bg-gray-400 dark:bg-gray-500 text-white rounded-md p-2">
                                        Driver
                                    </button>
                                @elseif ($seat->seat_type === 'is_not_seat')
                                    <button type="button" disabled
                                        class="bg-gray-400 dark:bg-gray-500 text-white rounded-md p-2">
                                        X
                                    </button>
                                @else
                                    <button type="button"
                                        class="bg-yellow-600 dark:bg-yellow-500 text-white rounded-md p-2">
                                        Available
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
