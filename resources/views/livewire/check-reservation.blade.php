<div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex items-center justify-center">
    <div class="max-w-2xl w-full sm:px-6 lg:px-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Check Reservation</h2>

        <div class="mb-4">
            <input type="text" wire:model="reservationCode" wire:keydown.enter="checkReservation"
                class="p-3 w-full border rounded bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200"
                placeholder="Enter Reservation Code">
            <button wire:click="checkReservation"
                class="mt-2 w-full p-3 bg-blue-500 dark:bg-blue-600 hover:bg-blue-700 text-white rounded">Check</button>
        </div>

        @if (session()->has('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($reservationDetails)
            <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Reservation Details</h3>
                <p class="text-gray-700 dark:text-gray-300"><strong>Reservation Code:</strong>
                    {{ $reservationDetails->code }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Name:</strong> {{ $reservationDetails->name }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Email:</strong>
                    {{ $this->maskEmail($reservationDetails->email) }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Phone:</strong>
                    {{ $this->maskPhone($reservationDetails->phone) }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Trip:</strong></p>
                <p class="text-gray-700 dark:text-gray-300">
                    From: <strong>{{ $reservationDetails->trip->route->fromPool->city->name }} -
                        {{ $reservationDetails->trip->route->fromPool->name }}</strong><br>
                    {{ $reservationDetails->trip->route->fromPool->address }}<br>
                    To: <strong>{{ $reservationDetails->trip->route->toPool->city->name }} -
                        {{ $reservationDetails->trip->route->toPool->name }}</strong><br>
                    {{ $reservationDetails->trip->route->toPool->address }}
                </p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Departure Time:</strong>
                    {{ \Carbon\Carbon::parse($reservationDetails->trip->departure_time)->format('l, d F Y - H:i') }}
                </p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Total Amount:</strong> Rp.
                    {{ number_format($reservationDetails->total_amount, 0, ',', '.') }}</p>
                <p class="text-gray-700 dark:text-gray-300"><strong>Status:</strong>
                    {{ ucfirst($reservationDetails->status) }}</p>

                @if ($reservationDetails->status === 'pending')
                    <p class="text-yellow-500 font-semibold mt-2">Please make payment to confirm your reservation.</p>
                @elseif ($reservationDetails->status === 'confirmed')
                    <p class="text-green-500 font-semibold mt-2">Please arrive at the pool 15 minutes before departure.
                    </p>
                @endif

                <h4 class="font-bold mt-4 text-gray-900 dark:text-white">Passengers</h4>
                <ul class="list-disc list-inside">
                    @foreach ($reservationDetails->passengers as $passenger)
                        <li class="text-gray-700 dark:text-gray-300">{{ $passenger->name }} (Seat
                            {{ $passenger->seat->seat_number }})</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div wire:loading wire:target="checkReservation"
        class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
        <div class="flex items-center justify-center min-h-screen">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
        </div>
    </div>
</div>
