<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{-- Menampilkan Judul Course --}}
                {{ $course->title }}
            </h2>
            <a href="{{ route('teacher.courses.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                &larr; Back to All Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Pesan Sukses (jika ada setelah update) --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- Layout Grid Dua Kolom --}}
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
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Course Materials</h3>
                            <a href="{{ route('teacher.courses.materials.create', $course) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">Add Material</a>
                        </div>
                        <div class="space-y-4">
                            @forelse ($course->materials as $material)
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                                    <span class="text-gray-800 dark:text-gray-200">{{ $material->title }}</span>
                                    <div>
                                        <a href="{{ route('teacher.courses.materials.edit', ['course' => $course->id, 'material' => $material->id]) }}" class="font-medium text-green-600 dark:text-green-400 hover:text-green-500 mr-3">Edit</a>
                                        {{-- Form untuk Delete Material --}}
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">No materials have been added to this course yet.</p>
                            @endforelse
                        </div>
                    </x-card>

                    {{-- Anda bisa menambahkan kartu untuk Jadwal Meeting di sini dengan gaya yang sama --}}

                </div>

                {{-- KOLOM KANAN (Sidebar Info) --}}
                <div class="md:col-span-1 space-y-6">

                    <x-card>
                        <dl>
                            <div class="mb-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Course Code</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $course->code }}</dd>
                            </div>
                            <div class="mb-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $course->duration_in_months ?? 'N/A' }} Months</dd>
                            </div>
                            {{-- Tambahkan info lain jika perlu --}}
                        </dl>
                    </x-card>

                    <x-card>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Enrolled Students & Grading</h3>
                        <div class="space-y-3">
                            @forelse ($course->enrolledStudents as $student)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-800 dark:text-gray-200">{{ $student->name }}</span>
                                    <a href="{{ route('teacher.courses.enrollments.grade.form', ['course' => $course->id, 'user' => $student->id]) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                        Enter Grade
                                    </a>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400">No students are currently enrolled.</p>
                            @endforelse
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>