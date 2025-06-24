<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Question to Quiz: ') }} <span class="text-indigo-600">{{ $quiz->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('teacher.quizzes.questions.store', $quiz) }}">
                        @csrf

                        <div>
                            <x-input-label for="question_text" :value="__('Question Text')" />
                            <input id="question_text" type="hidden" name="question_text" value="{{ old('question_text') }}">
                            <trix-editor input="question_text" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></trix-editor>
                            <x-input-error :messages="$errors->get('question_text')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="type" :value="__('Question Type')" />
                            <select name="type" id="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="single_choice">Multiple Choice (Single Answer)</option>
                                <option value="multiple_choice">Multiple Choice (Multiple Answers)</option>
                                <option value="essay">Essay</option>
                                <option value="image_choice">Image Choice</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="points" :value="__('Points')" />
                            <x-text-input id="points" class="block mt-1 w-full" type="number" name="points" :value="old('points', 10)" min="1" required />
                            <x-input-error :messages="$errors->get('points')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('teacher.quizzes.questions.index', $quiz) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Save Question') }}
                            </x-primary-button>
                        </div>
                    </form>

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