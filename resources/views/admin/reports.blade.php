<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Reports') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex gap-3">
                <a class="rounded-md px-3 py-2 text-sm font-semibold {{ $period === 'daily' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}" href="{{ route('admin.reports', ['period' => 'daily']) }}">{{ __('Daily') }}</a>
                <a class="rounded-md px-3 py-2 text-sm font-semibold {{ $period === 'weekly' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}" href="{{ route('admin.reports', ['period' => 'weekly']) }}">{{ __('Weekly') }}</a>
                <a class="rounded-md px-3 py-2 text-sm font-semibold {{ $period === 'monthly' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}" href="{{ route('admin.reports', ['period' => 'monthly']) }}">{{ __('Monthly') }}</a>
            </div>

            <div class="rounded-lg bg-white p-6 shadow space-y-2 text-gray-800">
                <p class="text-sm text-gray-500">{{ __('Period start') }}: {{ $start->timezone(config('app.timezone'))->toDayDateTimeString() }}</p>
                <p class="text-lg font-semibold">{{ __('Bookings created') }}: {{ $bookingsInRange }}</p>
                <p class="text-lg font-semibold">{{ __('Recorded revenue') }}: {{ number_format($revenue, 2) }}</p>
                <p class="text-sm text-gray-600">{{ __('Revenue totals successful payments logged when bookings are marked delivered.') }}</p>
            </div>
        </div>
    </div>
</x-admin-layout>
