<div class="fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                <button wire:click="closeModal()" type="button"
                    class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="sm:flex sm:items-start">
                <div
                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-gray-900 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-indigo-600 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-200" id="modal-title">
                        {{ $tripId ? 'Edit Trip' : 'Create Trip' }}
                    </h3>
                    <div class="mt-2">
                        <div class="mt-4">
                            <label for="vehicle_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vehicle</label>
                            <select wire:model="vehicle_id" id="vehicle_id" name="vehicle_id"
                                class="form-input border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm mt-1 block w-full"
                                {{ $tripId ? 'disabled' : '' }}>
                                <option value="">Select Vehicle</option>
                                @foreach ($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="departure_times"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Departure
                                Times</label>
                            @foreach ($departure_times as $index => $time)
                                <div class="flex items-center">
                                    <input type="datetime-local" wire:model="departure_times.{{ $index }}"
                                        id="departure_times.{{ $index }}"
                                        name="departure_times.{{ $index }}"
                                        class="form-input border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm mt-1 block w-full">
                                    @if (!$tripId)
                                        <button type="button" wire:click="removeDepartureTime({{ $index }})"
                                            class="ml-2 text-red-500 hover:text-red-700">
                                            ×
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                            @if (!$tripId)
                                <button type="button" wire:click="addDepartureTime()"
                                    class="mt-2 text-indigo-600 hover:text-indigo-800">
                                    + Add another departure time
                                </button>
                            @endif
                            @error('departure_times.*')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mt-4">
                            <label for="price"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price
                                (Rp)</label>
                            <input type="number" wire:model="price" id="price" name="price"
                                class="form-input border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded-md shadow-sm mt-1 block w-full">
                            @error('price')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button wire:click="store()" type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Save
                </button>
                <button wire:click="closeModal()" type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
    <div wire:loading wire:target="store" class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
        <div class="flex items-center justify-center min-h-screen">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
        </div>
    </div>
</div>
