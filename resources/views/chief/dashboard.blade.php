<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Executive Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Row for existing Users by Role and new User Verification Status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-medium mb-4">Total Users by Role</h3>
                            <canvas id="myChart"></canvas> {{-- Existing chart --}}
                        </div>
                        <div>
                            <h3 class="text-lg font-medium mb-4">User Verification Status</h3>
                            <canvas id="userVerificationStatusChart"></canvas> {{-- NEW PIE CHART --}}
                        </div>
                    </div>

                    {{-- Row for New User Registrations and Courses by Status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-medium mb-4">New User Registrations Over Time</h3>
                            <canvas id="userRegistrationTrendsChart"></canvas> {{-- NEW LINE CHART --}}
                        </div>
                        <div>
                            <h3 class="text-lg font-medium mb-4">Courses by Status</h3>
                            <canvas id="courseStatusChart"></canvas> {{-- NEW BAR CHART --}}
                        </div>
                    </div>

                    {{-- Add Download Report Buttons Here --}}
                    <div class="mt-8 text-right">
                        <h3 class="text-lg font-medium mb-4">Reports</h3>
                        <a href="{{ route('chief.reports.dashboard.excel') }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 focus:bg-green-600 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            Download Dashboard (Excel)
                        </a>
                        <a href="{{ route('chief.reports.dashboard.pdf') }}" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:bg-red-600 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Download Dashboard (PDF)
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // 1. Users by Role Chart
                const rawDataUsersByRole = @json($usersByRole);
                const labelsUsersByRole = rawDataUsersByRole.map(item => item.role.charAt(0).toUpperCase() + item.role.slice(1));
                const dataPointsUsersByRole = rawDataUsersByRole.map(item => item.count);

                new Chart(document.getElementById('myChart'), {
                    type: 'bar',
                    data: {
                        labels: labelsUsersByRole,
                        datasets: [{
                            label: 'Total Users',
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgb(54, 162, 235)',
                            data: dataPointsUsersByRole,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { position: 'top' }
                        }
                    }
                });

                // 2. User Verification Status (Pie Chart)
                const userVerificationStatus = @json($userVerificationStatus);
                new Chart(document.getElementById('userVerificationStatusChart'), {
                    type: 'pie',
                    data: {
                        labels: userVerificationStatus.labels,
                        datasets: [{
                            label: 'User Status',
                            data: userVerificationStatus.data,
                            backgroundColor: userVerificationStatus.backgroundColor,
                            borderColor: userVerificationStatus.borderColor,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: false }
                        }
                    }
                });

                // 3. New User Registrations Over Time (Line Chart)
                const userRegistrationTrends = @json($userRegistrationTrends);
                new Chart(document.getElementById('userRegistrationTrendsChart'), {
                    type: 'line',
                    data: {
                        labels: userRegistrationTrends.labels,
                        datasets: [{
                            label: userRegistrationTrends.label,
                            data: userRegistrationTrends.data,
                            borderColor: userRegistrationTrends.borderColor,
                            backgroundColor: userRegistrationTrends.backgroundColor,
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { display: true, position: 'top' }
                        }
                    }
                });

                // 4. Courses by Status (Bar Chart)
                const courseStatusData = @json($courseStatusData);
                new Chart(document.getElementById('courseStatusChart'), {
                    type: 'bar',
                    data: {
                        labels: courseStatusData.labels,
                        datasets: [{
                            label: 'Number of Courses',
                            data: courseStatusData.data,
                            backgroundColor: courseStatusData.backgroundColor,
                            borderColor: courseStatusData.borderColor,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { display: true, position: 'top' }
                        }
                    }
                });
            });
        </script>
    @endpush



</x-app-layout>