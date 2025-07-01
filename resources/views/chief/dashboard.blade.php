<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Executive Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Pie Chart Card --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">Courses by Division</h3>
                        {{-- Data is now passed via a data-attribute with unescaped quotes --}}
                        <canvas id="coursesByDivisionChart" data-chart='{!! json_encode($coursesByDivisionData) !!}'></canvas>
                    </div>
                </div>

                {{-- Line Chart Card --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                       <h3 class="text-lg font-medium mb-4">Enrollments Over Last 30 Days</h3>
                       {{-- Data is now passed via a data-attribute with unescaped quotes --}}
                       <canvas id="enrollmentsOverTimeChart" data-chart='{!! json_encode($enrollmentsOverTimeData) !!}'></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Add Chart.js library from CDN --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- PIE CHART SCRIPT ---
            const pieCanvas = document.getElementById('coursesByDivisionChart');
            if (pieCanvas) {
                const pieCtx = pieCanvas.getContext('2d');
                // Read and parse the data from the data-attribute
                const coursesByDivisionData = JSON.parse(pieCanvas.dataset.chart);

                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: coursesByDivisionData.labels,
                        datasets: [{
                            label: '# of Courses',
                            data: coursesByDivisionData.data,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(153, 102, 255, 0.7)',
                                'rgba(255, 159, 64, 0.7)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'top' } }
                    }
                });
            }

            // --- LINE CHART SCRIPT ---
            const lineCanvas = document.getElementById('enrollmentsOverTimeChart');
            if (lineCanvas) {
                const lineCtx = lineCanvas.getContext('2d');
                // Read and parse the data from the data-attribute
                const enrollmentsOverTimeData = JSON.parse(lineCanvas.dataset.chart);

                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: enrollmentsOverTimeData.labels,
                        datasets: [{
                            label: 'New Enrollments',
                            data: enrollmentsOverTimeData.data,
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    // Ensure only whole numbers are shown on the Y-axis
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
