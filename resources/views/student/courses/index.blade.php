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
                           <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                               @forelse ($courses as $course)
                                   <tr>
                                       <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $course->title }}</td>
                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $course->course_code ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $course->teacher?->name ?? 'N/A' }}</td> {{-- Access teacher name via relationship --}}
                                       <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                       <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{-- Make the title a link --}}
                                        <a href="{{ route('student.courses.show', $course->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ $course->title }}
                                        </a>
                                        </td>
                                        {{-- Add link to view course content later --}}
                                        {{-- <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">View</a> --}}
                                       </td>
                                   </tr>
                               @empty
                                   <tr>
                                       <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">You are not enrolled in any courses.</td>
                                   </tr>
                               @endforelse
                           </tbody>
                       </table>
                   </div>
                   <div class="mt-4">
                       {{ $courses->links() }} {{-- Pagination links --}}
                   </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>