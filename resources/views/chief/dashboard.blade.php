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
                    {{-- Row 1 --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-medium mb-4">Total Users by Role</h3>
                            <canvas id="myChart"></canvas>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium mb-4">User Verification Status</h3>
                            <canvas id="userVerificationStatusChart"></canvas>
                        </div>
                    </div>

                    {{-- Row 2 --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-medium mb-4">New User Registrations Over Time</h3>
                            <canvas id="userRegistrationTrendsChart"></canvas>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium mb-4">Courses by Status</h3>
                            <canvas id="courseStatusChart"></canvas>
                        </div>
                    </div>

                    {{-- Row 3: Daily Active Students --}}
                    <div class="grid grid-cols-1 gap-6 mb-8">
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <h3 class="text-lg font-medium mb-4">Siswa Aktif Harian (30 Hari Terakhir)</h3>
                            <div class="relative h-80 w-full"> 
                                <canvas id="dailyActiveStudentsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Download Buttons --}}
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
                // Skrip untuk 4 chart Anda yang sudah ada (biarkan seperti apa adanya)
                // 1. Users by Role Chart
                const rawDataUsersByRole = @json($usersByRole);
                new Chart(document.getElementById('myChart'), { /* ... konfigurasi Anda ... */ });

                // 2. User Verification Status
                const userVerificationStatus = @json($userVerificationStatus);
                new Chart(document.getElementById('userVerificationStatusChart'), { /* ... konfigurasi Anda ... */ });

                // 3. New User Registrations
                const userRegistrationTrends = @json($userRegistrationTrends);
                new Chart(document.getElementById('userRegistrationTrendsChart'), { /* ... konfigurasi Anda ... */ });

                // 4. Courses by Status
                const courseStatusData = @json($courseStatusData);
                new Chart(document.getElementById('courseStatusChart'), { /* ... konfigurasi Anda ... */ });


                // --- Skrip untuk diagram ke-5: Siswa Aktif Harian ---
                try {
                    const dasLabels = @json($dasLabels);
                    const dasData = @json($dasData);

                    // Hanya gambar chart jika ada data
                    if (dasLabels && dasLabels.length > 0) {
                        const dasCtx = document.getElementById('dailyActiveStudentsChart');
                        if (dasCtx) {
                            new Chart(dasCtx, {
                                type: 'line',
                                data: {
                                    labels: dasLabels,
                                    datasets: [{
                                        label: 'Jumlah Siswa Aktif',
                                        data: dasData,
                                        backgroundColor: 'rgba(22, 163, 74, 0.2)',
                                        borderColor: 'rgba(22, 163, 74, 1)',
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
                                            ticks: { precision: 0 }
                                        }
                                    }
                                }
                            });
                        }
                    } else {
                        // Tampilkan pesan jika tidak ada data
                        const canvasContainer = document.getElementById('dailyActiveStudentsChart').parentElement;
                        canvasContainer.innerHTML = `<div class="flex items-center justify-center h-full text-gray-500">Tidak ada data aktivitas siswa dalam 30 hari terakhir.</div>`;
                    }
                } catch (e) {
                    console.error("Gagal membuat diagram Siswa Aktif Harian:", e);
                }
            });
        </script>
    @endpush
</x-app-layout>