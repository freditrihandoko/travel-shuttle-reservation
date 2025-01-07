<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List of Vehicles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto text-center p-6">
                    <div class="flex justify-between mb-4">
                        <div class="flex">
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search Vehicles"
                                class="px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <a href="{{ route('vehicles.create') }}"
                                class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                Add New Vehicle
                            </a>
                        </div>
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
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Name
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Seat
                                    Count</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($vehicles as $vehicle)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                        {{ $vehicles->firstItem() + $loop->index }}</td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $vehicle->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700 dark:text-gray-200">
                                        {{ $vehicle->seat_count }}</td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        @if ($vehicle->trips()->exists())
                                            <span class="text-gray-500">Edit</span>
                                            <span class="text-gray-500">Delete</span>
                                        @else
                                            <a href="{{ route('vehicles.edit', $vehicle->id) }}"
                                                class="inline-block rounded bg-yellow-500 px-4 py-2 text-xs font-medium text-white hover:bg-yellow-600">
                                                Edit
                                            </a>
                                            <button wire:click="confirmDelete({{ $vehicle->id }})"
                                                class="inline-block rounded bg-red-500 px-4 py-2 text-xs font-medium text-white hover:bg-red-600">
                                                Delete
                                            </button>
                                        @endif
                                        <button wire:click="showVehicle({{ $vehicle->id }})"
                                            class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $vehicles->links() }}
                    </div>
                    <div wire:loading wire:target="showVehicle"
                        class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
                        <div class="flex items-center justify-center min-h-screen">
                            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500">
                            </div>
                        </div>
                    </div>
                    {{-- Vehicle Modal --}}
                    @if ($showVehicleModal)
                        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                            <div
                                class="mt-24 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-6 rounded shadow-lg">
                                <h2 class="text-xl font-bold mb-4">{{ $selectedVehicle->name }}</h2>
                                <p><strong>Seat Available:</strong> {{ $selectedVehicle->seat_count }}</p>

                                <div
                                    class="mt-4 grid grid-cols-{{ $cols }} grid-rows-{{ $rows }} gap-4">
                                    @foreach ($selectedVehicle->seat_layout as $rowIndex => $row)
                                        @foreach ($row as $colIndex => $seat)
                                            <div
                                                class="cursor-pointer p-4 border rounded-lg flex justify-center items-center flex-col 
                                    {{ $seat['seat_type'] == 'is_seat' ? 'bg-green-500' : ($seat['seat_type'] == 'driver' ? 'bg-amber-500' : 'bg-gray-200') }}">
                                                @if ($seat['seat_type'] == 'is_seat')
                                                    <span class="text-black mt-2">{{ $seat['seat_number'] }}</span>
                                                @elseif ($seat['seat_type'] == 'is_not_seat')
                                                    <span class="text-red-500 mt-2">X</span>
                                                @elseif ($seat['seat_type'] == 'driver')
                                                    <span class="text-white mt-2">Driver</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    <button wire:click="$set('showVehicleModal', false)"
                                        class="inline-block rounded bg-gray-600 px-4 py-2 text-xs font-medium text-white hover:bg-gray-700">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Delete Confirmation Modal --}}
                    @if ($showDeleteModal)
                        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                            <div class="bg-white p-6 rounded shadow-lg">
                                <h2 class="text-xl font-bold mb-4">Confirm Delete</h2>
                                <p>Are you sure you want to delete {{ $selectedVehicle->name }}?</p>
                                <div class="mt-4 flex justify-end space-x-4">
                                    <button wire:click="$set('showDeleteModal', false)"
                                        class="inline-block rounded bg-gray-600 px-4 py-2 text-xs font-medium text-white hover:bg-gray-700">
                                        Cancel
                                    </button>
                                    <button wire:click="delete"
                                        class="inline-block rounded bg-red-600 px-4 py-2 text-xs font-medium text-white hover:bg-red-700">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
