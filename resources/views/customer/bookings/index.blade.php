<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My bookings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <a href="{{ route('customer.bookings.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">{{ __('New booking') }}</a>

            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">#</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Status') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Pickup') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Total') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Payment') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="px-4 py-2">{{ $booking->id }}</td>
                                <td class="px-4 py-2">{{ $booking->status->label() }}</td>
                                <td class="px-4 py-2">{{ $booking->pickup_scheduled_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</td>
                                <td class="px-4 py-2">{{ number_format((float) $booking->total_amount, 2) }}</td>
                                <td class="px-4 py-2">
                                    @if ($booking->isPaid())
                                        <span class="text-green-700">{{ $booking->paymentChannelLabel() ?? __('Paid') }}</span>
                                    @elseif ((float) $booking->total_amount > 0)
                                        @if ($booking->payment_channel)
                                            <span class="text-amber-600">{{ $booking->paymentChannelLabel() }} — {{ __('Unpaid') }}</span>
                                        @else
                                            <span class="text-amber-600">{{ __('Unpaid') }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('customer.bookings.show', $booking) }}">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>{{ $bookings->links() }}</div>
        </div>
    </div>
</x-app-layout>
