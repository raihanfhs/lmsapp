<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 dark:text-gray-800 leading-tight">
            {{ __('My Enrolled Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                     <div class="overflow-x-auto">
                       <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                           <thead class="bg-gray-50 dark:bg-gray-700">
                               <tr>
                                   <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course Title</th>
                                   <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course Code</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teacher</th>
                                   <th scope="col" class="relative px-6 py-3">
                                       <span class="sr-only">View</span>
                                   </th>
                               </tr>
                           </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @forelse ($enrollments as $enrollment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('student.courses.show', $enrollment->course->id) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $enrollment->course->title }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $enrollment->course->course_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{-- This will now work correctly --}}
                                            {{ $enrollment->course->teachers->pluck('name')->join(', ') ?: 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            You are not enrolled in any courses yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                       </table>
                   </div>
                   <div class="mt-4">
                        {{-- Use the correct variable from the controller --}}
                        {{ $enrollments->links() }} {{-- Pagination links --}}
                   </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>