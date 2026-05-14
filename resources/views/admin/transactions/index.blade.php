<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Transactions') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">#</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Booking') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Amount') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Type') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Recorded') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($transactions as $tx)
                            <tr>
                                <td class="px-4 py-2">{{ $tx->id }}</td>
                                <td class="px-4 py-2">
                                    @if ($tx->booking)
                                        #{{ $tx->booking->id }} — {{ $tx->booking->customer?->name }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ number_format((float) $tx->amount, 2) }}</td>
                                <td class="px-4 py-2">{{ $tx->type }}</td>
                                <td class="px-4 py-2">{{ $tx->recorded_at?->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $transactions->links() }}</div>
        </div>
    </div>
</x-admin-layout>
