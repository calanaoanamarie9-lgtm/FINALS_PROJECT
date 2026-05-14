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
                    <p><span class="font-semibold">{{ __('Payment') }}:</span> {{ __('Paid') }} ({{ $booking->paymentChannelLabel() ?? __('Online') }}) @if ($booking->paid_at) — {{ $booking->paid_at->timezone(config('app.timezone'))->toDayDateTimeString() }} @endif</p>
                @elseif ((float) $booking->total_amount > 0)
                    <p><span class="font-semibold">{{ __('Payment') }}:</span> {{ $booking->paymentChannelLabel() ?? __('Not on file') }}</p>
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
