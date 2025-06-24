<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $course->title }}
            </h2>
            <a href="{{ route('student.courses.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                &larr; Back to My Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- KOLOM KIRI (Konten Utama) --}}
                <div class="md:col-span-2 space-y-6">
                    
                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">About This Course</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $course->description ?? 'No description provided.' }}
                        </p>
                    </x-card>

                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Course Materials</h3>
                        <div class="space-y-2">
                            @forelse ($course->materials as $material)
                                {{-- Di sini kita bisa membuat materi menjadi link atau menampilkan kontennya langsung --}}
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                    <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ $material->title }}</h4>
                                    {{-- Tampilkan konten Trix Editor dengan aman --}}
                                    <div class="prose dark:prose-invert max-w-none mt-2 text-gray-600 dark:text-gray-400">
                                        {!! $material->content !!}
                                    </div>
                                </div>

                                <div>
                                    <a href="{{ route('teacher.courses.materials.edit', ['course' => $course->id, 'material' => $material->id]) }}" class="font-medium text-green-600 dark:text-green-400 hover:text-green-500 mr-3">Edit</a>
                                    
                                    {{-- FORM UNTUK DELETE --}}
                                    <form action="{{ route('teacher.courses.materials.destroy', ['course' => $course->id, 'material' => $material->id]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 dark:text-red-400 hover:text-red-500" onclick="return confirm('Anda yakin ingin menghapus materi ini?')">Delete</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">The teacher has not added any materials to this course yet.</p>
                            @endforelse
                        </div>
                    </x-card>

                </div>

                {{-- KOLOM KANAN (Sidebar Info) --}}
                <div class="md:col-span-1 space-y-6">

                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Course Information</h3>
                        <dl>
                            <div class="mb-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Teacher(s)</dt>
                                <dd class="text-gray-900 dark:text-gray-100">
                                    {{ $course->teachers->pluck('name')->join(', ') }}
                                </dd>
                            </div>
                            <div class="mb-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Course Code</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $course->code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $course->duration_in_months ?? 'N/A' }} Months</dd>
                            </div>
                        </dl>
                    </x-card>

                    <x-card>
                         <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">My Progress</h3>
                         {{-- Di sini kita bisa tambahkan logika untuk menampilkan nilai dan tombol download sertifikat --}}
                         <p class="text-gray-500 dark:text-gray-400">Your final grade and certificate information will appear here.</p>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>