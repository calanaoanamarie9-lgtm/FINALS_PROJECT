<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Booking #:id', ['id' => $booking->id]) }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            @if (session('warning'))
                <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-900">{{ session('warning') }}</div>
            @endif
            <div class="rounded-lg bg-white p-6 shadow space-y-2 text-sm text-gray-800">
                <p class="text-2xl font-semibold text-indigo-700">{{ $booking->status->label() }}</p>
                <p class="text-xs text-gray-500">{{ __('Last updated') }}: {{ $booking->updated_at->timezone(config('app.timezone'))->toDayDateTimeString() }}</p>
                <p><span class="font-semibold">{{ __('Assigned staff') }}:</span> {{ $booking->assignedStaff?->name ?? __('Not assigned yet') }}</p>
                <p><span class="font-semibold">{{ __('Pickup') }}:</span> {{ $booking->pickup_scheduled_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                <p><span class="font-semibold">{{ __('Delivery') }}:</span> {{ $booking->delivery_scheduled_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                <p><span class="font-semibold">{{ __('Pickup address') }}:</span> {{ $booking->pickup_address }}</p>
                <p><span class="font-semibold">{{ __('Delivery address') }}:</span> {{ $booking->delivery_address }}</p>
                <p><span class="font-semibold">{{ __('Total') }}:</span> {{ number_format((float) $booking->total_amount, 2) }} <span class="text-gray-500">PHP</span></p>
                <p><span class="font-semibold">{{ __('Payment method') }}:</span> {{ $booking->paymentChannelLabel() ?? '—' }}</p>
                @if ($booking->isPaid())
                    <p class="text-sm font-medium text-green-700">
                        {{ __('Payment received') }}
                        @if ($booking->paid_at)
                            — {{ $booking->paid_at->timezone(config('app.timezone'))->toDayDateTimeString() }}
                        @endif
                    </p>
                @elseif ((float) $booking->total_amount > 0 && ! $booking->isCancelled())
                    <p class="text-amber-600 font-medium text-sm">{{ __('Unpaid') }}</p>
                    <form method="post" action="{{ route('customer.bookings.payment-method', $booking) }}" class="flex items-center gap-2 mt-2">
                        @csrf
                        @method('patch')
                        <select name="payment_method" class="rounded-md border-gray-300 text-sm shadow-sm">
                            @foreach (\App\Http\Controllers\Customer\BookingController::PAYMENT_METHODS as $value => $label)
                                <option value="{{ $value }}" @selected($booking->payment_channel === $value)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                        <x-primary-button type="submit">{{ __('Update') }}</x-primary-button>
                    </form>
                @endif
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Services') }}</h3>
                <ul class="mt-3 space-y-1 text-sm text-gray-700">
                    @foreach ($booking->items as $item)
                        <li>{{ $item->service?->name }} × {{ $item->quantity }} — {{ number_format((float) $item->line_total, 2) }}</li>
                    @endforeach
                </ul>
            </div>

            @if (! in_array($booking->status, [\App\Enums\BookingStatus::Delivered, \App\Enums\BookingStatus::Cancelled], true))
                <div class="rounded-lg bg-white p-6 shadow space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Reschedule') }}</h3>
                    <form method="post" action="{{ route('customer.bookings.reschedule', $booking) }}" class="grid gap-4 sm:grid-cols-2">
                        @csrf
                        @method('patch')
                        <div>
                            <x-input-label for="pickup_scheduled_at" :value="__('New pickup')" />
                            <x-text-input id="pickup_scheduled_at" name="pickup_scheduled_at" type="datetime-local" class="mt-1 block w-full" :value="old('pickup_scheduled_at', $booking->pickup_scheduled_at->format('Y-m-d\TH:i'))" required />
                        </div>
                        <div>
                            <x-input-label for="delivery_scheduled_at" :value="__('New delivery')" />
                            <x-text-input id="delivery_scheduled_at" name="delivery_scheduled_at" type="datetime-local" class="mt-1 block w-full" :value="old('delivery_scheduled_at', $booking->delivery_scheduled_at->format('Y-m-d\TH:i'))" required />
                        </div>
                        <div class="sm:col-span-2">
                            <x-primary-button type="submit">{{ __('Save new schedule') }}</x-primary-button>
                        </div>
                    </form>
                </div>

                <form method="post" action="{{ route('customer.bookings.cancel', $booking) }}" onsubmit="return confirm('{{ __('Cancel this booking?') }}');" class="rounded-lg bg-white p-6 shadow">
                    @csrf
                    <x-danger-button type="submit">{{ __('Cancel booking') }}</x-danger-button>
                </form>
            @endif

            @if ($booking->status === \App\Enums\BookingStatus::Delivered)
                <div class="rounded-lg bg-white p-6 shadow space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Rate your experience') }}</h3>
                    @if ($booking->review)
                        <p class="text-sm text-gray-700">{{ __('You rated this booking :n/5.', ['n' => $booking->review->rating]) }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->review->comment }}</p>
                    @else
                        <form method="post" action="{{ route('customer.bookings.review', $booking) }}" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="rating" :value="__('Rating (1-5)')" />
                                <select id="rating" name="rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <x-input-label for="comment" :value="__('Comments')" />
                                <textarea id="comment" name="comment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                            <x-primary-button type="submit">{{ __('Submit review') }}</x-primary-button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
