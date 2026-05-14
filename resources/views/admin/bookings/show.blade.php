<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Booking #:id', ['id' => $booking->id]) }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow space-y-3 text-sm text-gray-700">
                <p><span class="font-semibold">{{ __('Customer') }}:</span> {{ $booking->customer?->name }} ({{ $booking->customer?->email }})</p>
                <p><span class="font-semibold">{{ __('Phone') }}:</span> {{ $booking->customer?->phone ?? '—' }}</p>
                <p><span class="font-semibold">{{ __('Status') }}:</span> {{ $booking->status->label() }}</p>
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

                @if ((float) $booking->total_amount > 0)
                    <div class="pt-3 border-t border-gray-200 mt-3">
                        @if ($booking->isPaid())
                            <form method="post" action="{{ route('admin.bookings.mark-unpaid', $booking) }}" class="inline" onsubmit="return confirm('{{ __('Mark this payment as unpaid? This will clear payment records and transactions.') }}')">
                                @csrf
                                <x-danger-button type="submit">{{ __('Mark as Unpaid') }}</x-danger-button>
                            </form>
                        @else
                            <form method="post" action="{{ route('admin.bookings.mark-paid', $booking) }}" class="inline">
                                @csrf
                                <x-primary-button type="submit">{{ __('Mark as Paid') }}</x-primary-button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Line items') }}</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    @foreach ($booking->items as $item)
                        <li>{{ $item->service?->name }} × {{ $item->quantity }} — {{ number_format((float) $item->line_total, 2) }}</li>
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

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Assign & status') }}</h3>
                <form method="post" action="{{ route('admin.bookings.update', $booking) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('patch')
                    <div>
                        <x-input-label for="assigned_staff_id" :value="__('Assigned staff')" />
                        <select id="assigned_staff_id" name="assigned_staff_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach ($staffMembers as $staff)
                                <option value="{{ $staff->id }}" @selected($booking->assigned_staff_id === $staff->id)>{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('assigned_staff_id')" />
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach (\App\Enums\BookingStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($booking->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>
                    <x-primary-button>{{ __('Save changes') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
