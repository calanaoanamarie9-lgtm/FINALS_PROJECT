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

                <form method="post" action="{{ route('customer.bookings.cancel', $booking) }}" class="rounded-lg bg-white p-6 shadow" x-data @submit.prevent="Swal.fire({ title: '{{ __('Cancel booking?') }}', text: '{{ __('This action cannot be undone.') }}', icon: 'warning', showCancelButton: true, confirmButtonText: '{{ __('Yes, cancel it') }}', cancelButtonText: '{{ __('Keep booking') }}' }).then(r => { if (r.isConfirmed) $el.submit() })">
                    @csrf
                    <x-danger-button type="submit">{{ __('Cancel booking') }}</x-danger-button>
                </form>
            @endif

            @if ($booking->status === \App\Enums\BookingStatus::Delivered)
                {{-- Review --}}
                <div class="rounded-lg bg-white p-6 shadow space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Rate your experience') }}</h3>

                    @if ($booking->review)
                        <div class="flex items-center gap-1 mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $booking->review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        @if ($booking->review->comment)
                            <p class="text-sm text-gray-700">{{ $booking->review->comment }}</p>
                        @endif
                    @else
                        <form method="post" action="{{ route('customer.bookings.review', $booking) }}" class="space-y-4" x-data="{ rating: 0, hovered: 0 }">
                            @csrf
                            <div>
                                <x-input-label :value="__('Rating')" />
                                <div class="flex items-center gap-1 mt-1">
                                    <template x-for="i in 5" :key="i">
                                        <button type="button" @click="rating = i" @mouseenter="hovered = i" @mouseleave="hovered = 0" class="focus:outline-none">
                                            <svg class="w-8 h-8 transition-colors" :class="i <= (hovered || rating) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </button>
                                    </template>
                                    <input type="hidden" name="rating" x-bind:value="rating" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="comment" :value="__('Comments (optional)')" />
                                <textarea id="comment" name="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="{{ __('Tell us about your experience...') }}"></textarea>
                            </div>
                            <x-primary-button type="submit" x-bind:disabled="!rating">{{ __('Submit review') }}</x-primary-button>
                        </form>
                    @endif
                </div>

                {{-- Report a problem --}}
                <div class="rounded-lg bg-white p-6 shadow space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Report an issue') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('Let us know if you experienced any problems with your laundry.') }}</p>

                    @if ($booking->issues->isNotEmpty())
                        @foreach ($booking->issues as $issue)
                            <div class="rounded-md border border-gray-200 bg-gray-50 p-4 text-sm">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold {{ $issue->status === 'resolved' ? 'text-green-600' : 'text-amber-600' }}">
                                        {{ $issue->status === 'resolved' ? __('Resolved') : __('Open') }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $issue->created_at->timezone(config('app.timezone'))->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700">{{ $issue->description }}</p>
                            </div>
                        @endforeach
                    @endif

                    <form method="post" action="{{ route('customer.bookings.issue', $booking) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="issue_description" :value="__('Describe the issue')" />
                            <textarea id="issue_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="{{ __('Describe what went wrong...') }}" required></textarea>
                        </div>
                        <x-primary-button type="submit">{{ __('Submit issue') }}</x-primary-button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
