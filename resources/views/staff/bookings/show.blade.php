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
                    <p><span class="font-semibold">{{ __('Payment') }}:</span> <span class="text-green-700 font-medium">{{ __('Paid') }}</span> ({{ $booking->paymentChannelLabel() ?? __('Online') }}) @if ($booking->paid_at) — {{ $booking->paid_at->timezone(config('app.timezone'))->toDayDateTimeString() }} @endif</p>
                @elseif ((float) $booking->total_amount > 0)
                    <p><span class="font-semibold">{{ __('Payment') }}:</span> <span class="text-amber-600 font-medium">{{ __('Unpaid') }}</span>@if ($booking->payment_channel) ({{ $booking->paymentChannelLabel() }}) @endif</p>
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

            @if ($booking->review)
                <div class="rounded-lg bg-white p-6 shadow space-y-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Customer review') }}</h3>
                    <div class="flex items-center gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $booking->review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    @if ($booking->review->comment)
                        <p class="text-sm text-gray-700">{{ $booking->review->comment }}</p>
                    @endif
                </div>
            @endif

            @if ($booking->issues->isNotEmpty())
                <div class="rounded-lg bg-white p-6 shadow space-y-3">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Reported issues') }}</h3>
                    @foreach ($booking->issues as $issue)
                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3 text-sm">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-semibold {{ $issue->status === 'resolved' ? 'text-green-600' : 'text-amber-600' }}">
                                    {{ $issue->status === 'resolved' ? __('Resolved') : __('Open') }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $issue->created_at->timezone(config('app.timezone'))->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-700">{{ $issue->description }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            @php
                $steps = \App\Enums\BookingStatus::staffProgression();
                $currentIdx = array_search($booking->status, $steps, true);
            @endphp

            @if ($currentIdx !== false)
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Update Booking') }}</h3>

                    <div class="flex items-center justify-center">
                        @foreach ($steps as $i => $step)
                            @if ($i > 0)
                                <div class="flex-1 h-0.5 max-w-8 {{ $i <= $currentIdx ? 'bg-indigo-500' : 'bg-gray-200' }}"></div>
                            @endif
                            <div class="flex flex-col items-center">
                                <div @class([
                                    'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2',
                                    'bg-indigo-600 border-indigo-600 text-white' => $i < $currentIdx,
                                    'border-indigo-600 text-indigo-600 bg-indigo-50 ring-2 ring-indigo-200' => $i === $currentIdx,
                                    'border-gray-300 text-gray-400 bg-white' => $i > $currentIdx,
                                ])>
                                    @if ($i < $currentIdx)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>
                                <span @class([
                                    'mt-1.5 text-xs font-semibold text-center',
                                    'text-indigo-700' => $i <= $currentIdx,
                                    'text-gray-400' => $i > $currentIdx,
                                ])>{{ $step->label() }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($currentIdx < count($steps) - 1)
                        <form method="post" action="{{ route('staff.bookings.advance', $booking) }}" class="mt-6 text-center">
                            @csrf
                            <x-primary-button type="submit">{{ __('Update Booking → :next', ['next' => $steps[$currentIdx + 1]->label()]) }}</x-primary-button>
                        </form>
                    @else
                        <p class="mt-6 text-sm text-center text-green-600 font-medium">{{ __('This booking has been delivered.') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
