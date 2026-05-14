<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Services & pricing') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div>
                <a href="{{ route('admin.services.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">{{ __('Add service') }}</a>
            </div>
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Name') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Price') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Unit') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Active') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($services as $service)
                            <tr>
                                <td class="px-4 py-2">{{ $service->name }}</td>
                                <td class="px-4 py-2">{{ number_format((float) $service->price, 2) }}</td>
                                <td class="px-4 py-2">{{ $service->unit }}</td>
                                <td class="px-4 py-2">{{ $service->is_active ? __('Yes') : __('No') }}</td>
                                <td class="px-4 py-2 text-right space-x-3">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.services.edit', $service) }}">{{ __('Edit') }}</a>
                                    <form class="inline" method="post" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('{{ __('Delete this service?') }}');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>{{ $services->links() }}</div>
        </div>
    </div>
</x-admin-layout>
