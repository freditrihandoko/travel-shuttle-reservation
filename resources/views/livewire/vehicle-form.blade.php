@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@endpush
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $vehicleId ? 'Edit Vehicle' : 'Create Vehicle' }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-6 gap-6 dark:text-white">
                        <div class="col-span-6 sm:col-span-3">
                            <label for="name" class="text-sm font-medium text-gray-900 dark:text-white block mb-2">
                                Vehicle Name</label>
                            <input type="text" wire:model="name" name="name" id="name"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5">
                            @error('name')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="seat_count"
                                class="text-sm font-medium text-gray-900 dark:text-white block mb-2">
                                Seat Available</label>
                            <input type="number" wire:model.live="seat_count" name="seat_count" id="seat_count"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5">
                            @error('seat_count')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="rows"
                                class="text-sm font-medium text-gray-900 dark:text-white block mb-2">Rows</label>
                            <input type="number" wire:model.live="rows" name="rows" id="rows"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="cols"
                                class="text-sm font-medium text-gray-900 dark:text-white block mb-2">Cols</label>
                            <input type="number" wire:model.live="cols" name="cols" id="cols"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5">
                        </div>
                    </div>

                    <div class="flex justify-center mt-4">
                        <button type="button"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded {{ $mode === 'seat' ? 'ring-2 ring-yellow-500' : '' }}"
                            wire:click="setMode('seat')">Seat Mode</button>
                        <button type="button"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded ml-2 {{ $mode === 'driver' ? 'ring-2 ring-indigo-500' : '' }}"
                            wire:click="setMode('driver')">Driver Mode</button>
                    </div>

                    @if ($error_message)
                        <div class="text-red-500 mt-4">
                            {{ $error_message }}
                        </div>
                    @endif

                    <div class="mt-6"
                        style="display: grid; grid-template-columns: repeat({{ $cols }}, minmax(0, 1fr)); gap: 8px;">
                        @foreach ($seat_layout as $rowIndex => $row)
                            @foreach ($row as $colIndex => $seat)
                                <div wire:click="toggleSeat({{ $rowIndex }}, {{ $colIndex }})"
                                    class="cursor-pointer p-4 border rounded-lg flex justify-center items-center flex-col 
                            {{ $seat['seat_type'] == 'is_seat' ? 'bg-green-500' : ($seat['seat_type'] == 'driver' ? 'bg-amber-500' : 'bg-gray-200') }}">
                                    @if ($seat['seat_type'] == 'is_seat')
                                        <i class="fas fa-user text-white text-2xl"></i>
                                        <span class="text-black mt-2">{{ $seat['seat_number'] }}</span>
                                    @elseif ($seat['seat_type'] == 'driver')
                                        <i class="fas fa-user-tie text-white text-2xl"></i>
                                        <span class="text-white mt-2">Driver</span>
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Save
                            Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
