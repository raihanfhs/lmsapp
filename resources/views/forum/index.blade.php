<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Diskusi: {{ $course->name }}
            </h2>
            {{-- Tombol ini akan kita fungsikan di langkah selanjutnya --}}
            <a href="{{ route('forum.create', $course->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Mulai Diskusi Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 divide-y divide-gray-200">
                    @forelse ($threads as $thread)
                        <div class="py-4 flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" src="{{ $thread->user->profile->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($thread->user->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ $thread->user->name }}">
                            </div>
                            <div class="flex-grow">
                                <a href="{{ route('forum.show', ['course' => $course->id, 'thread' => $thread->id]) }}" class="text-lg font-semibold text-gray-800 hover:text-blue-600">
                                    {{ $thread->title }}
                                </a>
                                <p class="text-sm text-gray-600 mt-1">
                                    Dimulai oleh {{ $thread->user->name }} &bull; {{ $thread->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 text-center w-24">
                                <span class="font-bold text-lg">{{ $thread->posts->count() }}</span>
                                <span class="text-sm text-gray-500 block">Post</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <h3 class="text-lg font-medium text-gray-900">Belum ada diskusi</h3>
                            <p class="mt-1 text-sm text-gray-500">Jadilah yang pertama memulai diskusi di mata kuliah ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
             <div class="mt-6">
                {{ $threads->links() }}
            </div>
        </div>
    </div>
</x-app-layout>