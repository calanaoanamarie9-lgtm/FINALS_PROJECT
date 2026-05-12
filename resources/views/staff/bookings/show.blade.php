<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Booking #:id', ['id' => $booking->id]) }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow space-y-2 text-sm text-gray-800">
                <p class="text-2xl font-semibold text-indigo-700">{{ $booking->status->label() }}</p>
                <p><span class="font-semibold">{{ __('Customer') }}:</span> {{ $booking->customer?->name }}</p>
                <p><span class="font-semibold">{{ __('Email') }}:</span> {{ $booking->customer?->email }}</p>
                <p><span class="font-semibold">{{ __('Phone') }}:</span> {{ $booking->customer?->phone ?? '—' }}</p>
                <p><span class="font-semibold">{{ __('Pickup') }}:</span> {{ $booking->pickup_scheduled_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                <p><span class="font-semibold">{{ __('Delivery') }}:</span> {{ $booking->delivery_scheduled_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                <p><span class="font-semibold">{{ __('Pickup address') }}:</span> {{ $booking->pickup_address }}</p>
                <p><span class="font-semibold">{{ __('Delivery address') }}:</span> {{ $booking->delivery_address }}</p>
                <p><span class="font-semibold">{{ __('Total') }}:</span> {{ number_format((float) $booking->total_amount, 2) }} PHP</p>
                @if ($booking->isPaid())
                    <p><span class="font-semibold">{{ __('Payment') }}:</span> {{ __('Paid') }} ({{ $booking->paymentChannelLabel() ?? __('Online') }})</p>
                @elseif ((float) $booking->total_amount > 0)
                    <p><span class="font-semibold">{{ __('Payment') }}:</span> {{ __('Not on file') }}</p>
                @endif
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Services') }}</h3>
                <ul class="mt-3 space-y-1 text-sm text-gray-700">
                    @foreach ($booking->items as $item)
                        <li>{{ $item->service?->name }} × {{ $item->quantity }}</li>
                    @endforeach
                </ul>
            </div>

            @if (! in_array($booking->status, [\App\Enums\BookingStatus::Delivered, \App\Enums\BookingStatus::Cancelled], true))
                <form method="post" action="{{ route('staff.bookings.advance', $booking) }}" class="rounded-lg bg-white p-6 shadow">
                    @csrf
                    <p class="text-sm text-gray-600 mb-3">{{ __('Advance the booking one step along the workflow.') }}</p>
                    <x-primary-button type="submit">{{ __('Advance status') }}</x-primary-button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
