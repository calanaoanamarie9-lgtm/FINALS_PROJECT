<x-guest-layout>
    <div class="py-10 text-center space-y-6">
        <p class="text-sm uppercase tracking-wide text-indigo-600 font-semibold">{{ __('CleanSwift Laundry System') }}</p>
        <h1 class="text-4xl font-bold text-gray-900">{{ __('Clean Clothes, Zero Hassle') }}</h1>
        <p class="max-w-2xl mx-auto text-gray-600">{{ __('Fast, reliable, and affordable laundry service. We wash, dry, fold, and care for your clothes with freshness you can trust.') }}</p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('services.index') }}" class="inline-flex items-center rounded-md bg-yellow-400 px-5 py-2.5 text-sm font-semibold text-gray-900 shadow hover:bg-yellow-300">{{ __('Services') }}</a>
            <a href="{{ route('services.index') }}" class="inline-flex items-center rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-500">{{ __('Pricing') }}</a>
            <a href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-red-500">{{ __('Register') }}</a>
            <a href="{{ route('login') }}" class="inline-flex items-center rounded-md bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-500">{{ __('Sign in') }}</a>
        </div>
        <p class="text-sm text-gray-500">{{ __('Staff member?') }} <a class="text-indigo-600 font-semibold hover:underline" href="{{ route('register.staff') }}">{{ __('Submit a staff application') }}</a></p>
    </div>
</x-guest-layout>
