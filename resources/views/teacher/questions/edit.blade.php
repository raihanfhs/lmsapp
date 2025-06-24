<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Question') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('teacher.quizzes.questions.update', ['quiz' => $quiz, 'question' => $question]) }}">@csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="question_text" :value="__('Question Text')" />
                            <input id="question_text" type="hidden" name="question_text" value="{{ old('question_text', $question->question_text) }}">
                            <trix-editor input="question_text" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></trix-editor>
                            <x-input-error :messages="$errors->get('question_text')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="type" :value="__('Question Type')" />
                            <select name="type" id="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="single_choice" @selected(old('type', $question->type) == 'single_choice')>Multiple Choice (Single Answer)</option>
                                <option value="multiple_choice" @selected(old('type', $question->type) == 'multiple_choice')>Multiple Choice (Multiple Answers)</option>
                                <option value="essay" @selected(old('type', $question->type) == 'essay')>Essay</option>
                                <option value="image_choice" @selected(old('type', $question->type) == 'image_choice')>Image Choice</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="points" :value="__('Points')" />
                            <x-text-input id="points" class="block mt-1 w-full" type="number" name="points" :value="old('points', $question->points)" min="1" required />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('teacher.quizzes.questions.index', $quiz) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Question') }}
                            </x-primary-button>
                        </div>
                    </form>
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900">Answer Options</h3>

                        <div class="mt-4 space-y-2">
                            @forelse ($question->options as $option)
                                <div class="flex justify-between items-center p-3 border rounded-md hover:bg-gray-50">

                                    <p class="{{ $option->is_correct ? 'text-green-600 font-bold' : '' }}">
                                        {{ $option->option_text }}
                                        @if ($option->is_correct)
                                            <span class="text-xs text-green-700">(Correct Answer)</span>
                                        @endif
                                    </p>

                                    <form method="POST" action="{{ route('teacher.options.destroy', $option) }}" onsubmit="return confirm('Are you sure you want to delete this option?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded bg-red-500 px-3 py-1 text-xs font-medium text-white hover:bg-red-600">
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No options added yet.</p>
                            @endforelse
                        </div>

                        <div class="mt-6">
                            <h4 class="font-medium text-gray-800">Add New Option</h4>
                            <form method="POST" action="{{ route('teacher.questions.options.store', $question) }}" class="mt-2 flex items-center space-x-2">
                                @csrf
                                <div class="flex-grow">
                                    <x-text-input id="option_text" class="block w-full" type="text" name="option_text" required />
                                </div>
                                <div class="flex items-center">
                                    <input id="is_correct" name="is_correct" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                    <label for="is_correct" class="ml-2 block text-sm text-gray-900">Correct</label>
                                </div>
                                <div>
                                    <x-primary-button>
                                        {{ __('Add') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <script>
        document.addEventListener("trix-attachment-add", function(event) {
            // Cek apakah yang dilampirkan adalah file
            if (event.attachment.file) {
                uploadFileAttachment(event.attachment);
            }
        });
    
        function uploadFileAttachment(attachment) {
            // Buat FormData untuk mengirim file
            const formData = new FormData();
            formData.append("file", attachment.file);
    
            // Kirim request ke server menggunakan fetch API
            fetch('{{ route("trix.store") }}', { // Menggunakan route name untuk URL yang lebih aman
                method: 'POST',
                body: formData,
                headers: {
                    // Jangan lupa sertakan CSRF Token Laravel
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Jika response server tidak OK (misal: error 500 atau 422)
                    // kita lemparkan error untuk ditangkap oleh .catch()
                    throw new Error(`Server responded with ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Setelah berhasil, Trix akan menggunakan URL ini untuk menampilkan gambar
                attachment.setAttributes({
                    url: data.url,
                    href: data.url // href diperlukan agar gambar bisa diklik
                });
            })
            .catch(error => {
                console.error('Trix upload error:', error);
                // Hapus attachment dari editor jika upload gagal
                attachment.remove(); 
                alert("Gagal mengupload gambar. Pastikan file adalah gambar dan ukurannya tidak lebih dari 2MB.");
            });
        }
    </script>
</x-app-layout>