<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Diskusi: {{ $course->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $thread->title }}</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Dimulai oleh {{ $thread->user->name }} &bull; {{ $thread->created_at->diffForHumans() }}
                </p>
            </div>

            <div class="space-y-6">
                @foreach ($thread->posts as $post)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 flex space-x-4">
                            <div class="flex-shrink-0 text-center w-24">
                                <img class="h-12 w-12 rounded-full mx-auto" src="{{ $post->user->profile->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($post->user->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ $post->user->name }}">
                                <p class="font-semibold mt-2 text-sm">{{ $post->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $post->user->roles->first()->name ?? 'Student' }}</p>
                            </div>

                            <div class="flex-grow border-l border-gray-200 pl-6">
                                <p class="text-xs text-gray-500 mb-2">
                                    Diposting {{ $post->created_at->diffForHumans() }}
                                </p>
                                <div class="prose max-w-none">
                                    {!! nl2br(e($post->body)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Tulis Balasan Anda</h3>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form method="POST" action="{{ route('posts.store', $thread->id) }}">
                            @csrf
                            <div>
                                <textarea id="body" name="body" rows="6" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tulis balasan Anda di sini...">{{ old('body') }}</textarea>
                                <x-input-error :messages="$errors->get('body')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>
                                    {{ __('Kirim Balasan') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>