@php
    use Carbon\Carbon;
@endphp
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List of Trips Reservation') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search routes..."
                    class="px-4 py-2 border rounded dark:bg-gray-800 dark:text-gray-200" />

                <div class="grid grid-cols-3 gap-4 mt-4">
                    @foreach ($routes as $route)
                        <div class="bg-white dark:bg-gray-900 dark:text-gray-200 p-4 shadow rounded-lg cursor-pointer"
                            wire:click="selectRoute({{ $route->id }})">
                            <p>{{ $route->fromPool->city->name }} - {{ $route->fromPool->name }} to
                                {{ $route->toPool->city->name }} - {{ $route->toPool->name }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $routes->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
