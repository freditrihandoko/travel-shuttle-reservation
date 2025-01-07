@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener('livewire:load', function() {
            $('#from_pool_id').select2();
            $('#to_pool_id').select2();
            $('#from_pool_id').on('change', function(e) {
                var data = $('#from_pool_id').select2("val");
                @this.set('from_pool_id', data);
            });
            $('#to_pool_id').on('change', function(e) {
                var data = $('#to_pool_id').select2("val");
                @this.set('to_pool_id', data);
            });

            Livewire.hook('message.processed', (message, component) => {
                $('#from_pool_id').select2();
                $('#to_pool_id').select2();
            });

            window.livewire.on('routeUpdated', () => {
                $('#from_pool_id').select2();
                $('#to_pool_id').select2();
            });
        });
    </script>
@endpush

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List of Routes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto text-center p-6">
                    <div class="flex justify-between mb-4">
                        <div class="flex">
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search Routes"
                                class="px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <button wire:click="showCreateModal"
                                class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                Add New Route
                            </button>
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
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">From
                                    Pool</th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">To
                                    Pool</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($routes as $route)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                        {{ $routes->firstItem() + $loop->index }}</td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $route->fromPool->name }} ({{ $route->fromPool->city->name }})
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $route->toPool->name }} ({{ $route->toPool->city->name }})
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        <button wire:click="showEditModal({{ $route->id }})"
                                            class="inline-block rounded bg-yellow-500 px-4 py-2 text-xs font-medium text-white hover:bg-yellow-600">
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $route->id }})"
                                            class="inline-block rounded bg-red-500 px-4 py-2 text-xs font-medium text-white hover:bg-red-600">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $routes->links() }}
                    </div>

                    @if ($showRouteModal)
                        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50"
                            wire:keydown.escape="closeModal">
                            <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded shadow-lg w-1/2"
                                wire:click.away="closeModal">
                                <h2 class="text-xl font-bold mb-4">{{ $routeId ? 'Edit Route' : 'Add New Route' }}</h2>
                                <form wire:submit.prevent="saveRoute">
                                    <div class="mb-4">
                                        <label for="from_pool_id" class="block text-gray-700 dark:text-gray-200">From
                                            Pool</label>
                                        <select id="from_pool_id" wire:model.live="from_pool_id"
                                            class="border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded px-4 py-2 w-full"
                                            @if ($routeId) disabled @endif>
                                            <option value="">Select Pool</option>
                                            @foreach ($pools as $pool)
                                                <option value="{{ $pool->id }}">{{ $pool->name }}
                                                    ({{ $pool->city->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('from_pool_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="to_pool_id" class="block text-gray-700 dark:text-gray-200">To
                                            Pool</label>
                                        <select id="to_pool_id" wire:model.live="to_pool_id"
                                            class="border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded px-4 py-2 w-full"
                                            @if ($routeId) disabled @endif>
                                            <option value="">Select Pool</option>
                                            @foreach ($pools as $pool)
                                                @if ($pool->id != $from_pool_id)
                                                    <option value="{{ $pool->id }}">{{ $pool->name }}
                                                        ({{ $pool->city->name }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('to_pool_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="button" wire:click="$set('showRouteModal', false)"
                                            class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                                        <button type="submit"
                                            class="bg-indigo-600 text-white px-4 py-2 rounded">{{ $routeId ? 'Update' : 'Save' }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Delete Confirmation Modal --}}
                    @if ($showDeleteModal)
                        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                            <div
                                class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded shadow-lg">
                                <h2 class="text-xl font-bold mb-4">Delete Confirmation</h2>
                                <p>Are you sure you want to delete this route?</p>
                                <div class="flex justify-end mt-4">
                                    <button type="button" wire:click="$set('showDeleteModal', false)"
                                        class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                                    <button type="button" wire:click="deleteRoute"
                                        class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div wire:loading wire:target="saveRoute"
                    class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
                    <div class="flex items-center justify-center min-h-screen">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
                    </div>
                </div>
                <div wire:loading wire:target="deleteRoute"
                    class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
                    <div class="flex items-center justify-center min-h-screen">
                        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
