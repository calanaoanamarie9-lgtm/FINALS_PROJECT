<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin — CleanSwift') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-white p-6 shadow">
                    <p class="text-sm text-gray-500">{{ __('Bookings today') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $todayBookings }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow">
                    <p class="text-sm text-gray-500">{{ __('Revenue today') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($revenueToday, 2) }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow">
                    <p class="text-sm text-gray-500">{{ __('Pending staff applications') }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $pendingStaff }}</p>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Quick links') }}</h3>
                <ul class="mt-4 list-disc space-y-2 ps-5 text-gray-700">
                    <li><a class="text-indigo-600 hover:underline" href="{{ route('admin.staff-applications.index') }}">{{ __('Review staff applications') }}</a></li>
                    <li><a class="text-indigo-600 hover:underline" href="{{ route('admin.bookings.index') }}">{{ __('Manage bookings') }}</a></li>
                    <li><a class="text-indigo-600 hover:underline" href="{{ route('admin.services.index') }}">{{ __('Service pricing') }}</a></li>
                    <li><a class="text-indigo-600 hover:underline" href="{{ route('admin.reports') }}">{{ __('Reports') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</x-admin-layout>
