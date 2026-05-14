<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit service') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow">
                <form method="post" action="{{ route('admin.services.update', $service) }}" class="space-y-4">
                    @csrf
                    @method('patch')
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $service->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <x-input-label for="slug" :value="__('Slug')" />
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $service->slug)" />
                        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                    </div>
                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $service->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>
                    <div>
                        <x-input-label for="price" :value="__('Price')" />
                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $service->price)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('price')" />
                    </div>
                    <div>
                        <x-input-label for="unit" :value="__('Unit')" />
                        <x-text-input id="unit" name="unit" type="text" class="mt-1 block w-full" :value="old('unit', $service->unit)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('unit')" />
                    </div>
                    <div>
                        <x-input-label for="sort_order" :value="__('Sort order')" />
                        <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', $service->sort_order)" />
                        <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('is_active', $service->is_active))>
                        <x-input-label for="is_active" :value="__('Active')" />
                    </div>
                    <div class="flex gap-3">
                        <x-primary-button>{{ __('Update') }}</x-primary-button>
                        <a href="{{ route('admin.services.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
