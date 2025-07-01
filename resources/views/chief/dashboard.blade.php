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

                    {{-- Add Download Report Buttons Here (will implement in next step) --}}
                    <div class="mt-8 text-right">
                        <h3 class="text-lg font-medium mb-4">Reports</h3>
                        {{-- Buttons will go here --}}
                    </div>

                </div>
            </div>
        </div>
    </div>

    @@push('scripts')
    {{-- Memuat library Chart.js dari CDN (menggunakan 'auto' untuk memastikan semua komponen dimuat dan terdaftar secara internal) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Debugging: Log the Chart object and its properties ---
            // This is primarily for diagnostics if the 'auto' CDN doesn't fix it.
            // console.log('Chart global object:', Chart);
            // console.log('Chart.controllers:', Chart.controllers);
            // console.log('Chart.elements:', Chart.elements);
            // console.log('Chart.scales:', Chart.scales);
            // console.log('Chart.plugins:', Chart.plugins);
            // --- End Debugging ---

            // With chart.js/auto, controllers and other elements are often
            // registered automatically. If they are not, you would typically
            // register them like this:
            // Chart.register(
            //     Chart.controllers.bar,
            //     Chart.controllers.line,
            //     Chart.controllers.pie,
            //     Chart.elements.arc,
            //     Chart.scales.category,
            //     Chart.scales.linear,
            //     Chart.plugins.title,
            //     Chart.plugins.tooltip,
            //     Chart.plugins.legend
            // );

            // If using chart.js/auto, the explicit Chart.register calls for *individual controllers*
            // might not be necessary, as 'auto' registers everything. The issue is likely
            // that the previous manual Chart.register was incorrect or redundant.
            // Let's remove the explicit Chart.register calls for now with `chart.js/auto`.


            // 1. Users by Role Chart (existing bar chart)
            const rawDataUsersByRole = @json($usersByRole);
            const labelsUsersByRole = rawDataUsersByRole.map(item => item.role.charAt(0).toUpperCase() + item.role.slice(1));
            const dataPointsUsersByRole = rawDataUsersByRole.map(item => item.count);

            new Chart(
                document.getElementById('myChart'),
                {
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
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                }
            );

            // 2. User Verification Status Chart (NEW PIE CHART)
            const userVerificationStatus = @json($userVerificationStatus);
            new Chart(
                document.getElementById('userVerificationStatusChart'),
                {
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
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false,
                            }
                        }
                    }
                }
            );

            // 3. New User Registrations Over Time Chart (NEW LINE CHART)
            const userRegistrationTrends = @json($userRegistrationTrends);
            new Chart(
                document.getElementById('userRegistrationTrendsChart'),
                {
                    type: 'line',
                    data: {
                        labels: userRegistrationTrends.labels,
                        datasets: [{
                            label: 'New User Registrations', // Use a default label or pass from controller
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
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            }
                        }
                    }
                }
            );

            // 4. Courses by Status Chart (NEW BAR CHART)
            const courseStatusData = @json($courseStatusData);
            new Chart(
                document.getElementById('courseStatusChart'),
                {
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
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            }
                        }
                    }
                }
            );

        });
    </script>
    @endpush
</x-app-layout>