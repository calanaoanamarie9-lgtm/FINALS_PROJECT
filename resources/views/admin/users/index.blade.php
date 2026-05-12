<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Users') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex flex-wrap gap-3 items-center justify-between">
                <form method="get" class="flex gap-2 items-end">
                    <div>
                        <x-input-label for="role" :value="__('Role')" />
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            @foreach (\App\Enums\UserRole::cases() as $r)
                                <option value="{{ $r->value }}" @selected($role === $r->value)>{{ ucfirst($r->value) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button type="submit">{{ __('Filter') }}</x-primary-button>
                </form>
                <a href="{{ route('admin.users.staff.create') }}" class="text-sm font-semibold text-indigo-600 hover:underline">{{ __('Create staff account') }}</a>
            </div>

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Name') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Email') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Role') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Active') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-4 py-2">{{ $user->name }}</td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2">{{ $user->role->value }}</td>
                                <td class="px-4 py-2">{{ $user->is_active ? __('Yes') : __('No') }}</td>
                                <td class="px-4 py-2 text-right space-x-3">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.users.edit', $user) }}">{{ __('Edit') }}</a>
                                    @if (! $user->isAdmin())
                                        <form class="inline" method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('Delete user?') }}');">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>
