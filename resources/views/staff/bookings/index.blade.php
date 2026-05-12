<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Assigned bookings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">#</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Customer') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Status') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Pickup') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="px-4 py-2">{{ $booking->id }}</td>
                                <td class="px-4 py-2">{{ $booking->customer?->name }}</td>
                                <td class="px-4 py-2">{{ $booking->status->label() }}</td>
                                <td class="px-4 py-2">{{ $booking->pickup_scheduled_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('staff.bookings.show', $booking) }}">{{ __('Open') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $bookings->links() }}</div>
        </div>
    </div>
</x-app-layout>
