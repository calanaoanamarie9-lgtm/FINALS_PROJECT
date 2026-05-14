<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff applications') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Name') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Email') }}</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">{{ __('Phone') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($applications as $user)
                            <tr>
                                <td class="px-4 py-2">{{ $user->name }}</td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2">{{ $user->phone ?? '—' }}</td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <form class="inline" method="post" action="{{ route('admin.staff-applications.approve', $user) }}">
                                        @csrf
                                        <x-primary-button type="submit">{{ __('Approve') }}</x-primary-button>
                                    </form>
                                    <form class="inline" method="post" action="{{ route('admin.staff-applications.reject', $user) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded-md border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50">{{ __('Reject') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-600">{{ __('No pending applications.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $applications->links() }}</div>
        </div>
    </div>
</x-admin-layout>
