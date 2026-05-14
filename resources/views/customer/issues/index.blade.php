<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Reported Issues') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($issues as $issue)
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-semibold {{ $issue->status === 'resolved' ? 'text-green-600' : 'text-amber-600' }}">
                                    {{ $issue->status === 'resolved' ? __('Resolved') : __('Open') }}
                                </span>
                                <span class="text-xs text-gray-400">{{ __('Booking #:id', ['id' => $issue->booking_id]) }}</span>
                                <span class="text-xs text-gray-400">{{ $issue->created_at->timezone(config('app.timezone'))->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 mt-1">{{ $issue->description }}</p>
                        </div>
                        <a href="{{ route('customer.bookings.show', $issue->booking) }}" class="text-xs font-semibold text-indigo-600 hover:underline shrink-0">{{ __('View booking') }}</a>
                    </div>
                </div>
            @empty
                <div class="rounded-lg bg-white p-8 shadow-sm text-center text-gray-500">
                    {{ __('You have not reported any issues.') }}
                </div>
            @endforelse

            <div>{{ $issues->links() }}</div>
        </div>
    </div>
</x-app-layout>
