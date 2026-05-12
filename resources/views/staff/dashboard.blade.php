<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">{{ __('Open assigned tasks') }}</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $assignedOpen }}</p>
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Upcoming pickups') }}</h3>
                <ul class="mt-4 divide-y divide-gray-100">
                    @forelse ($upcoming as $booking)
                        <li class="py-3 flex justify-between gap-4">
                            <div>
                                <p class="font-medium text-gray-900">#{{ $booking->id }} — {{ $booking->customer?->name }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->pickup_scheduled_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                            </div>
                            <a class="text-indigo-600 text-sm font-semibold hover:underline" href="{{ route('staff.bookings.show', $booking) }}">{{ __('View') }}</a>
                        </li>
                    @empty
                        <li class="py-4 text-gray-600">{{ __('No upcoming assignments.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
