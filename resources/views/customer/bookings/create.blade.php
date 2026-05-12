<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Book laundry') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow space-y-6">
                @if (session('warning'))
                    <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-900">{{ session('warning') }}</div>
                @endif

                <form method="post" action="{{ route('customer.bookings.store') }}" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="pickup_address" :value="__('Pickup address')" />
                        <textarea id="pickup_address" name="pickup_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('pickup_address') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('pickup_address')" />
                    </div>
                    <div>
                        <x-input-label for="delivery_address" :value="__('Delivery address')" />
                        <textarea id="delivery_address" name="delivery_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('delivery_address') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('delivery_address')" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="pickup_scheduled_at" :value="__('Pickup date & time')" />
                            <x-text-input id="pickup_scheduled_at" name="pickup_scheduled_at" type="datetime-local" class="mt-1 block w-full" :value="old('pickup_scheduled_at')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('pickup_scheduled_at')" />
                        </div>
                        <div>
                            <x-input-label for="delivery_scheduled_at" :value="__('Delivery date & time')" />
                            <x-text-input id="delivery_scheduled_at" name="delivery_scheduled_at" type="datetime-local" class="mt-1 block w-full" :value="old('delivery_scheduled_at')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('delivery_scheduled_at')" />
                        </div>
                    </div>
                    <div>
                        <x-input-label :value="__('Payment method')" />
                        <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-3" x-data="{ selected: '{{ old('payment_method') }}' }">
                            <input type="hidden" name="payment_method" x-model="selected" />
                            <template x-for="method in {{ json_encode([
                                ['value' => 'cod', 'label' => 'Cash on Delivery'],
                                ['value' => 'gcash', 'label' => 'GCash'],
                                ['value' => 'maya', 'label' => 'Maya'],
                                ['value' => 'bank_transfer', 'label' => 'Bank Transfer'],
                            ]) }}" :key="method.value">
                                <button type="button"
                                    class="flex flex-col items-center gap-2 rounded-lg border-2 p-4 text-sm font-medium transition-all duration-150"
                                    :class="selected === method.value ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-200' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50'"
                                    @click="selected = method.value">
                                    <template x-if="method.value === 'cod'">
                                        <svg class="w-8 h-8" :class="selected === method.value ? 'text-indigo-600' : 'text-gray-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M9 12h6"/></svg>
                                    </template>
                                    <template x-if="method.value === 'gcash'">
                                        <svg class="w-8 h-8" :class="selected === method.value ? 'text-indigo-600' : 'text-gray-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 8h8M8 12h6M8 16h4"/></svg>
                                    </template>
                                    <template x-if="method.value === 'maya'">
                                        <svg class="w-8 h-8" :class="selected === method.value ? 'text-indigo-600' : 'text-gray-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="12" cy="12" r="4"/><path d="M9 12l2 2 4-4"/></svg>
                                    </template>
                                    <template x-if="method.value === 'bank_transfer'">
                                        <svg class="w-8 h-8" :class="selected === method.value ? 'text-indigo-600' : 'text-gray-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10l9-7 9 7v11H3z"/><path d="M8 14h3v7H8zM13 14h3v7h-3z"/></svg>
                                    </template>
                                    <span x-text="method.label"></span>
                                </button>
                            </template>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes (optional)')" />
                        <textarea id="notes" name="notes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Services & quantities') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Enter quantities for each service you need.') }}</p>
                        <div class="mt-4 space-y-3">
                            @foreach ($services as $service)
                                <div class="flex items-center justify-between gap-4 rounded-md border border-gray-100 p-3">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $service->name }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format((float) $service->price, 2) }} / {{ $service->unit }}</p>
                                    </div>
                                    <div class="w-28">
                                        <x-input-label :for="'items_'.$service->id" :value="__('Qty')" />
                                        <x-text-input :id="'items_'.$service->id" name="items[{{ $service->id }}]" type="number" min="0" class="mt-1 block w-full" :value="old('items.'.$service->id, (int) data_get(collect(old('items', []))->firstWhere('service_id', (int) $service->id), 'quantity', 0))" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('items')" />
                    </div>

                    <x-primary-button type="submit">{{ __('Submit booking') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
