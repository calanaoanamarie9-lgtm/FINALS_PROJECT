<x-guest-layout>
    <div class="py-10 text-center space-y-6">
        <p class="text-sm uppercase tracking-wide text-indigo-600 font-semibold">{{ __('CleanSwift Laundry System') }}</p>
        <h1 class="text-4xl font-bold text-gray-900">{{ __('Spotless clothes. Swift service.') }}</h1>
        <p class="max-w-2xl mx-auto text-gray-600">{{ __('Book wash, dry, fold, and ironing online. Track pickups and deliveries, pay securely, and rate every order.') }}</p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('services.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500">{{ __('Browse services') }}</a>
            <a href="{{ route('register') }}" class="inline-flex items-center rounded-md border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-800 hover:bg-gray-50">{{ __('Customer sign up') }}</a>
            <a href="{{ route('login') }}" class="inline-flex items-center rounded-md border border-transparent px-5 py-2.5 text-sm font-semibold text-indigo-700 hover:underline">{{ __('Sign in') }}</a>
        </div>
        <p class="text-sm text-gray-500">{{ __('Staff member?') }} <a class="text-indigo-600 font-semibold hover:underline" href="{{ route('register.staff') }}">{{ __('Submit a staff application') }}</a></p>
    </div>
</x-guest-layout>
