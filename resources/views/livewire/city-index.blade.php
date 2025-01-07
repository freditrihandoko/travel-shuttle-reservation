<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List of Cities') }}
        </h2>
    </x-slot>
    <div wire:loading wire:target="saveCity" class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
        <div class="flex items-center justify-center min-h-screen">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto text-center p-6">
                    <div class="flex justify-between mb-4">
                        <div class="flex">
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search Cities"
                                class="px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <button wire:click="showCreateModal"
                                class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                Add New City
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
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($cities as $city)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-900 dark:text-white">
                                        {{ $cities->firstItem() + $loop->index }}</td>
                                    <td
                                        class="whitespace-nowrap px-4 py-2 font-medium text-gray-700 dark:text-gray-200">
                                        {{ $city->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        <button wire:click="showEditModal({{ $city->id }})"
                                            class="inline-block rounded bg-yellow-500 px-4 py-2 text-xs font-medium text-white hover:bg-yellow-600">
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $city->id }})"
                                            class="inline-block rounded bg-red-500 px-4 py-2 text-xs font-medium text-white hover:bg-red-600">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $cities->links() }}
                    </div>


                    {{-- City Modal --}}
                    @if ($showCityModal)

                        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50"
                            wire:keydown.escape="closeModal">
                            <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded shadow-lg w-1/3"
                                wire:click.away="closeModal">
                                <h2 class="text-xl font-bold mb-4">{{ $cityId ? 'Edit City' : 'Add New City' }}</h2>
                                <form wire:submit.prevent="saveCity">
                                    <div class="mb-4">
                                        <label for="name" class="block text-gray-700 dark:text-gray-200">City
                                            Name</label>
                                        <input type="text" id="name" wire:model="name"
                                            wire:keydown.enter.prevent="saveCity"
                                            class="border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded px-4 py-2 w-full">
                                        @error('name')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mt-4 flex justify-end space-x-4">
                                        <button type="button" wire:click="closeModal"
                                            class="inline-block rounded bg-gray-600 px-4 py-2 text-xs font-medium text-white hover:bg-gray-700">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="inline-block rounded bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                            {{ $cityId ? 'Update' : 'Save' }}
                                        </button>
                                    </div>
                                </form>


                            </div>
                        </div>

                        @script
                            <script>
                                console.log('wedus');
                            </script>
                        @endscript
                    @endif

                    {{-- Delete Confirmation Modal --}}
                    @if ($showDeleteModal)
                        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                            <div
                                class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-6 rounded shadow-lg">
                                <h2 class="text-xl font-bold mb-4">Confirm Delete</h2>
                                <p>Are you sure you want to delete this city?</p>
                                <div class="mt-4 flex justify-end space-x-4">
                                    <button wire:click="$set('showDeleteModal', false)"
                                        class="inline-block rounded bg-gray-600 px-4 py-2 text-xs font-medium text-white hover:bg-gray-700">
                                        Cancel
                                    </button>
                                    <button wire:click="deleteCity"
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
