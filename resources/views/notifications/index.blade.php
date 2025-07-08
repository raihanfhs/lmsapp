<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notification History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="divide-y divide-gray-200">
                        @forelse ($notifications as $notification)
                            <div class="py-4 flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    @if ($notification->read_at)
                                        <span class="h-3 w-3 rounded-full bg-gray-300 block" title="Read"></span>
                                    @else
                                        <span class="h-3 w-3 rounded-full bg-blue-500 block" title="Unread"></span>
                                    @endif
                                </div>

                                <div class="flex-grow">
                                    <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="hover:underline">
                                        <p class="{{ $notification->read_at ? 'text-gray-600' : 'font-bold text-gray-900' }}">
                                            {{ $notification->data['message'] ?? 'New notification.' }}
                                        </p>
                                    </a>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-gray-500">
                                You have no notifications.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>