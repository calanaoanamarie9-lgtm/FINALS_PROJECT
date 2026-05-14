<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Notifications') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @foreach ($notifications as $notification)
                @php $data = $notification->data; @endphp
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-sm flex justify-between gap-4">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $data['title'] ?? __('Notification') }}</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $data['body'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->timezone(config('app.timezone'))->diffForHumans() }}</p>
                    </div>
                    @if (! $notification->read_at)
                        <form method="post" action="{{ route('admin.notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-indigo-600 hover:underline">{{ __('Mark read') }}</button>
                        </form>
                    @endif
                </div>
            @endforeach

            <div>{{ $notifications->links() }}</div>
        </div>
    </div>
</x-admin-layout>
