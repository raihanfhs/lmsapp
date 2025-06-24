<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Learning Paths') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 rounded-lg bg-emerald-100 p-4 text-sm text-emerald-700" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('pengelola.learning-paths.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Create New Learning Path') }}
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm">
                            <thead class="ltr:text-left rtl:text-right">
                                <tr>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Title</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Status</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Total Courses</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($learningPaths as $path)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">{{ $path->title }}</td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700">
                                        @if ($path->is_active)
                                            <span class="inline-flex items-center justify-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-emerald-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center rounded-full bg-gray-100 px-2.5 py-0.5 text-gray-700">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700 text-center">{{ $path->courses->count() }}</td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('pengelola.learning-paths.edit', $path) }}" class="rounded bg-blue-500 px-4 py-2 text-xs font-medium text-white hover:bg-blue-600">Edit</a>

                                            <form 
                                                method="POST" 
                                                action="{{ route('pengelola.learning-paths.destroy', $path) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this learning path? This action cannot be undone.');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded bg-red-500 px-4 py-2 text-xs font-medium text-white hover:bg-red-600">
                                                    Delete
                                                </button>
                                            </form>
                                            </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">
                                        No learning paths found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $learningPaths->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>