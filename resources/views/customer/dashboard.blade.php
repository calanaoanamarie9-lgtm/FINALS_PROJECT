<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My CleanSwift') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-lg bg-white p-6 shadow flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Next booking') }}</h3>
                    @if ($upcomingBooking)
                        <p class="mt-2 text-gray-700">#{{ $upcomingBooking->id }} — {{ $upcomingBooking->status->label() }}</p>
                        <p class="text-sm text-gray-500">{{ __('Pickup:') }} {{ $upcomingBooking->pickup_scheduled_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                    @else
                        <p class="mt-2 text-gray-600">{{ __('You have no active bookings.') }}</p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('customer.bookings.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">{{ __('Book laundry') }}</a>
                    <a href="{{ route('customer.bookings.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Booking history') }}</a>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Popular services') }}</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach ($services as $service)
                        <div class="rounded-md border border-gray-100 p-4">
                            <p class="font-semibold text-gray-900">{{ $service->name }}</p>
                            <p class="text-sm text-gray-600">{{ $service->description }}</p>
                            <p class="mt-2 text-indigo-600 font-semibold">{{ __('From') }} {{ number_format((float) $service->price, 2) }} / {{ $service->unit }}</p>
                        </div>
                    @endforeach
                </div>
                <a class="mt-4 inline-block text-sm font-semibold text-indigo-600 hover:underline" href="{{ route('services.index') }}">{{ __('View all services') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
