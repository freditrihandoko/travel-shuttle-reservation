@php
    use Carbon\Carbon;
@endphp
<div>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('trips.index') }}" class="mr-4">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-500"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"   d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $route->fromPool->city->name }} - {{ $route->fromPool->name }} to {{ $route->toPool->city->name }} -
                {{ $route->toPool->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div
                    class="p-6 overflow-x-auto text-center bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

                    <button wire:click="openModal()"
                        class="mb-4 inline-block rounded bg-indigo-500 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-600">
                        Add New Trip
                    </button>
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

                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search trips..."
                        class="form-input border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm mt-1 mb-4 block w-full" />
                    <input type="date" wire:model.live.debounce.300ms="departure_date"
                        class="form-input border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm mt-1 mb-4 block w-full" />

                    <table
                        class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm dark:divide-gray-700 dark:bg-gray-900 rounded-lg overflow-hidden">
                        <thead class="ltr:text-left rtl:text-right">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">No
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    <button wire:click="sortBy('vehicle_id')" class="flex items-center">
                                        Vehicle
                                        @if ($sortField === 'vehicle_id')
                                            @if ($sortDirection === 'asc')
                                                <svg class="ml-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="ml-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Route
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    <button wire:click="sortBy('departure_time')" class="flex items-center">
                                        Departure Time
                                        @if ($sortField === 'departure_time')
                                            @if ($sortDirection === 'asc')
                                                <svg class="ml-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="ml-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    <button wire:click="sortBy('price')" class="flex items-center">
                                        Price
                                        @if ($sortField === 'price')
                                            @if ($sortDirection === 'asc')
                                                <svg class="ml-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="ml-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($trips as $trip)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                        {{ $trips->firstItem() + $loop->index }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $trip->vehicle->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                        {{ $trip->route->fromPool->city->name }} - {{ $trip->route->fromPool->name }}
                                        to
                                        {{ $trip->route->toPool->city->name }} - {{ $trip->route->toPool->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                        {{ Carbon::parse($trip->departure_time)->format('l, d F Y - H:i') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                        Rp {{ number_format($trip->price, 0, ',', '.') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        <button wire:click="edit({{ $trip->id }})"
                                            class="inline-block rounded bg-yellow-500 px-4 py-2 text-xs font-medium text-white hover:bg-yellow-600">
                                            Edit
                                        </button>
                                        <button wire:click="openDeleteModal({{ $trip->id }})"
                                            class="inline-block rounded bg-red-500 px-4 py-2 text-xs font-medium text-white hover:bg-red-600">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($trips->isEmpty())
                        <p class="text-center py-4 text-gray-600 dark:text-gray-400">
                            No trip found.<br>
                            Please change the sorting date or search keywords!
                        </p>
                    @endif

                    <div class="mt-4">
                        {{ $trips->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    @if ($isModalOpen)
        @include('livewire.trip-modal')
    @endif

    @if ($isDeleteModalOpen)
        @include('livewire.trip-delete-modal')
    @endif
</div>
