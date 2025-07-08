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
                            <canvas id="userVerificationStatusChart"></canvas> {{-- Existing chart --}}
                        </div>
                    </div>

                    {{-- Row for New User Registrations and Courses by Status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-medium mb-4">New User Registrations Over Time</h3>
                            <canvas id="userRegistrationTrendsChart"></canvas> {{-- Existing chart --}}
                        </div>
                        <div>
                            <h3 class="text-lg font-medium mb-4">Courses by Status</h3>
                            <canvas id="courseStatusChart"></canvas> {{-- Existing chart --}}
                        </div>
                    </div>

                    {{-- --- PENAMBAHAN 1: ROW BARU UNTUK DIAGRAM KETERLIBATAN --- --}}
                    <div class="grid grid-cols-1 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                             <h3 class="text-lg font-medium mb-4">Siswa Aktif Harian (30 Hari Terakhir)</h3>
                            <div class="h-80">
                                <canvas id="dailyActiveStudentsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Download Report Buttons --}}
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
                // ... (Kode Chart 1, 2, 3, 4 Anda yang sudah ada, biarkan saja)
                // 1. Users by Role Chart
                const rawDataUsersByRole = @json($usersByRole);
                // ... (lanjutan kode chart 1)

                // 2. User Verification Status (Pie Chart)
                const userVerificationStatus = @json($userVerificationStatus);
                // ... (lanjutan kode chart 2)

                // 3. New User Registrations Over Time (Line Chart)
                const userRegistrationTrends = @json($userRegistrationTrends);
                // ... (lanjutan kode chart 3)

                // 4. Courses by Status (Bar Chart)
                const courseStatusData = @json($courseStatusData);
                // ... (lanjutan kode chart 4)

                {{-- --- PENAMBAHAN 2: SKRIP UNTUK DIAGRAM BARU --- --}}
                // 5. Daily Active Students Chart
                const dasLabels = @json($dasLabels);
                const dasData = @json($dasData);
                new Chart(document.getElementById('dailyActiveStudentsChart'), {
                    type: 'line',
                    data: {
                        labels: dasLabels,
                        datasets: [{
                            label: 'Jumlah Siswa Aktif',
                            data: dasData,
                            backgroundColor: 'rgba(22, 163, 74, 0.2)', // Greenish background
                            borderColor: 'rgba(22, 163, 74, 1)',     // Greenish border
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0 // Memastikan tidak ada angka desimal di sumbu Y
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>