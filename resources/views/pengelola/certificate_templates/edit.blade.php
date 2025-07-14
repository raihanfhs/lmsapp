<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Template Sertifikat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form ini mengarah ke route 'update' dan menggunakan method 'PUT' --}}
                    <form action="{{ route('pengelola.certificate-templates.update', $certificateTemplate) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Template</label>
                            {{-- Menggunakan data lama ($certificateTemplate->name) untuk mengisi value --}}
                            <input type="text" name="name" id="name" value="{{ old('name', $certificateTemplate->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="background_image" class="block text-sm font-medium text-gray-700">Ganti Gambar Latar (Opsional)</label>
                            <input type="file" name="background_image" id="background_image" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                            <p class="mt-2 text-sm text-gray-600">Gambar saat ini:</p>
                            <img src="{{ Storage::url($certificateTemplate->background_image_path) }}" alt="Current Background" class="mt-2 h-24 w-48 object-cover rounded-md">
                        </div>
                        
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700">Konten Sertifikat</label>
                             {{-- Menggunakan data lama ($certificateTemplate->content) untuk mengisi textarea --}}
                            <textarea name="content" id="content" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>{{ old('content', $certificateTemplate->content) }}</textarea>
                            <p class="mt-2 text-sm text-gray-500">
                                Gunakan placeholder: <strong>{nama_peserta}</strong>, <strong>{nama_kursus}</strong>, <strong>{tanggal_selesai}</strong>.
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('pengelola.certificate-templates.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Perbarui Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>