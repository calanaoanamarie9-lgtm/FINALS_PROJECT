<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Bookings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-wrap gap-3 items-end justify-between">
                <form method="get" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            @foreach (\App\Enums\BookingStatus::cases() as $s)
                                <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button type="submit">{{ __('Filter') }}</x-primary-button>
                </form>

                <form method="post" action="{{ route('admin.bookings.destroy-all') }}" onsubmit="return confirm('{{ __('Delete all bookings? This cannot be undone.') }}')">
                    @csrf
                    @method('delete')
                    <x-danger-button type="submit">{{ __('Delete All') }}</x-danger-button>
                </form>
            </div>

            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">#</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Customer') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Status') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Staff') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Total') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Payment') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="px-4 py-2">{{ $booking->id }}</td>
                                <td class="px-4 py-2">{{ $booking->customer?->name }}</td>
                                <td class="px-4 py-2">{{ $booking->status->label() }}</td>
                                <td class="px-4 py-2">{{ $booking->assignedStaff?->name ?? '—' }}</td>
                                <td class="px-4 py-2">{{ number_format((float) $booking->total_amount, 2) }}</td>
                                <td class="px-4 py-2">
                                    @if ($booking->isPaid())
                                        <span class="text-green-700">{{ $booking->paymentChannelLabel() ?? __('Paid') }}</span>
                                    @elseif ((float) $booking->total_amount > 0)
                                        <span class="text-gray-400">{{ $booking->paymentChannelLabel() ?? '—' }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.bookings.show', $booking) }}">{{ __('Manage') }}</a>
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
