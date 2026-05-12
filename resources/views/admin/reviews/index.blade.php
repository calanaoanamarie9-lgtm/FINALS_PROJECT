<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Customer reviews') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @foreach ($reviews as $review)
                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="flex justify-between gap-4">
                        <div>
                            <p class="font-semibold text-gray-900">{{ __('Booking') }} #{{ $review->booking_id }}</p>
                            <p class="text-sm text-gray-600">{{ $review->customer?->name }}</p>
                        </div>
                        <p class="text-lg font-semibold text-indigo-600">{{ $review->rating }}/5</p>
                    </div>
                    <p class="mt-3 text-gray-700">{{ $review->comment ?: __('No written feedback.') }}</p>
                </div>
            @endforeach

            <div>{{ $reviews->links() }}</div>
        </div>
    </div>
</x-app-layout>
