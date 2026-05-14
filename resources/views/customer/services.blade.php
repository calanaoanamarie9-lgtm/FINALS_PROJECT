<x-guest-layout>
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-semibold text-gray-900">{{ __('Laundry services') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('Transparent pricing for Wash, Dry, Fold, Iron, and more.') }}</p>

        <div class="mt-8 grid gap-6 sm:grid-cols-2">
            @foreach ($services as $service)
                <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $service->name }}</h2>
                    <p class="mt-2 text-sm text-gray-600">{{ $service->description }}</p>
                    <p class="mt-4 text-lg font-bold text-indigo-600">{{ number_format((float) $service->price, 2) }} <span class="text-sm font-normal text-gray-500">/ {{ $service->unit }}</span></p>
                </div>
            @endforeach
        </div>


    </div>
</x-guest-layout>
