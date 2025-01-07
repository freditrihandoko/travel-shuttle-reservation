<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List of Pools') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto text-center p-6">
                    <div class="flex justify-between mb-4">
                        <div class="flex">
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search Pools"
                                class="px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <button wire:click="showCreateModal"
                                class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                Add New Pool
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
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">Name
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">City
                                </th>
                                <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    Address</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($pools as $pool)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                        {{ $pools->firstItem() + $loop->index }}</td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $pool->name }}</td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $pool->city->name }}</td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $pool->address }}</td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        <button wire:click="showEditModal({{ $pool->id }})"
                                            class="inline-block rounded bg-yellow-500 px-4 py-2 text-xs font-medium text-white hover:bg-yellow-600">
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $pool->id }})"
                                            class="inline-block rounded bg-red-500 px-4 py-2 text-xs font-medium text-white hover:bg-red-600">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $pools->links() }}
                    </div>

                    {{-- Pool Modal --}}
                    @if ($showPoolModal)
                        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50"
                            wire:keydown.escape="closeModal">
                            <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded shadow-lg w-1/2"
                                wire:click.away="closeModal">
                                <h2 class="text-xl font-bold mb-4">{{ $poolId ? 'Edit Pool' : 'Add New Pool' }}</h2>
                                <form wire:submit.prevent="savePool">
                                    <div class="mb-4">
                                        <label for="city_id"
                                            class="block text-gray-700 dark:text-gray-200">City</label>
                                        <select id="city_id" wire:model="city_id"
                                            class="border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded px-4 py-2 w-full"
                                            @if ($poolId) disabled @endif>
                                            <option value="">Select City</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('city_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="name" class="block text-gray-700 dark:text-gray-200">Pool
                                            Name</label>
                                        <input type="text" id="name" wire:model="name"
                                            class="border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded px-4 py-2 w-full">
                                        @error('name')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="address"
                                            class="block text-gray-700 dark:text-gray-200">Address</label>
                                        <textarea id="address" wire:model="address"
                                            class="border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded px-4 py-2 w-full"></textarea>
                                        @error('address')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="button" wire:click="$set('showPoolModal', false)"
                                            class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                                        <button type="submit"
                                            class="bg-indigo-600 text-white px-4 py-2 rounded">{{ $poolId ? 'Update' : 'Save' }}</button>
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
                                <p>Are you sure you want to delete this pool?</p>
                                <div class="flex justify-end mt-4">
                                    <button type="button" wire:click="$set('showDeleteModal', false)"
                                        class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                                    <button type="button" wire:click="deletePool"
                                        class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div wire:loading wire:target="savePool"
                        class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
                        <div class="flex items-center justify-center min-h-screen">
                            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500">
                            </div>
                        </div>
                    </div>
                    <div wire:loading wire:target="deletePool"
                        class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
                        <div class="flex items-center justify-center min-h-screen">
                            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
