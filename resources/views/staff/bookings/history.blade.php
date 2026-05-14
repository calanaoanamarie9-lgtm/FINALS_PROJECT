<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Booking History') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-gray-600">{{ __('Completed and cancelled bookings assigned to you.') }}</p>
                <a href="{{ route('staff.bookings.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">{{ __('← Back to current bookings') }}</a>
            </div>

            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">#</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Customer') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Status') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Total') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Payment') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Completed') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="px-4 py-2">{{ $booking->id }}</td>
                                <td class="px-4 py-2">{{ $booking->customer?->name }}</td>
                                <td class="px-4 py-2">{{ $booking->status->label() }}</td>
                                <td class="px-4 py-2">{{ number_format((float) $booking->total_amount, 2) }}</td>
                                <td class="px-4 py-2">
                                    @if ($booking->isPaid())
                                        <span class="text-green-700 font-medium">{{ __('Paid') }}</span>
                                    @elseif ((float) $booking->total_amount > 0)
                                        <span class="text-amber-600 font-medium">{{ __('Unpaid') }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $booking->updated_at->timezone(config('app.timezone'))->format('M j, Y') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('staff.bookings.show', $booking) }}">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">{{ __('No booking history yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $bookings->links() }}</div>
        </div>
    </div>
</x-app-layout>
