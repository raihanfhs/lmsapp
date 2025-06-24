<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Quiz History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y-2 divide-gray-200 bg-white text-sm">
                            <thead class="ltr:text-left rtl:text-right">
                                <tr>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Quiz Title</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Date Taken</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Score</th>
                                    <th class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">Status</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($attempts as $attempt)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900">{{ $attempt->quiz->title }}</td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700">{{ $attempt->end_time ? \Carbon\Carbon::parse($attempt->end_time)->format('d F Y, H:i') : 'In Progress' }}</td>
                                    <td class="whitespace-nowrap px-4 py-2 font-bold {{ $attempt->score >= $attempt->quiz->passing_grade ? 'text-green-600' : 'text-red-600' }}">{{ number_format($attempt->score, 2) }}%</td>
                                    <td class="whitespace-nowrap px-4 py-2 text-gray-700">
                                        @if ($attempt->score >= $attempt->quiz->passing_grade)
                                            <span class="inline-flex items-center justify-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-emerald-700">
                                                Passed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center rounded-full bg-red-100 px-2.5 py-0.5 text-red-700">
                                                Failed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-2">
                                        <a href="{{ route('student.quiz_attempts.results', $attempt) }}" class="rounded bg-blue-500 px-4 py-2 text-xs font-medium text-white hover:bg-blue-600">View Results</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">
                                        You have not attempted any quizzes yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $attempts->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>