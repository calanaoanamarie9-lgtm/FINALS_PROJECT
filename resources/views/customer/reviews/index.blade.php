<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Reviews') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($reviews as $review)
                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Booking #:id', ['id' => $review->booking_id]) }}</p>
                            <div class="flex items-center gap-1 mt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            @if ($review->comment)
                                <p class="text-sm text-gray-700 mt-2">{{ $review->comment }}</p>
                            @endif
                        </div>
                        <a href="{{ route('customer.bookings.show', $review->booking) }}" class="text-xs font-semibold text-indigo-600 hover:underline shrink-0">{{ __('View booking') }}</a>
                    </div>
                </div>
            @empty
                <div class="rounded-lg bg-white p-8 shadow-sm text-center text-gray-500">
                    {{ __('You have not reviewed any bookings yet.') }}
                </div>
            @endforelse

            <div>{{ $reviews->links() }}</div>
        </div>
    </div>
</x-app-layout>
