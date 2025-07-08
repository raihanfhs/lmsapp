<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Executive Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4 space-x-2">
                        <a href="{{ route('chief.dashboard.export.pdf') }}" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded">
                            Download PDF
                        </a>
                        <a href="{{ route('chief.dashboard.export.excel') }}" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">
                            Download Excel
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="font-semibold text-lg mb-2">Total Users by Role</h3>
                            <div style="position: relative; height: 350px;">
                                <canvas id="myChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="font-semibold text-lg mb-2">User Verification Status</h3>
                            <div style="position: relative; height: 350px;">
                                <canvas id="userVerificationStatusChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="font-semibold text-lg mb-2">New User Registrations Over Time</h3>
                            <div style="position: relative; height: 350px;">
                                <canvas id="userRegistrationTrendsChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="font-semibold text-lg mb-2">Courses by Status</h3>
                            <div style="position: relative; height: 350px;">
                                <canvas id="courseStatusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 bg-white p-4 rounded-lg shadow">
                        <h3 class="font-semibold text-lg mb-2">Daily Active Students (Last 30 Days)</h3>
                        <div style="position: relative; height: 350px;">
                            <canvas id="dailyActiveStudentsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Menunggu sampai seluruh halaman HTML selesai dimuat
            document.addEventListener('DOMContentLoaded', function () {

                // 1. Chart: Total Pengguna Berdasarkan Peran (Users by Role)
                const usersByRoleCtx = document.getElementById('myChart');
                if (usersByRoleCtx) {
                    const usersByRoleData = @json($usersByRole ?? []); 
                    new Chart(usersByRoleCtx, {
                        type: 'pie',
                        data: {
                            labels: usersByRoleData.map(d => d.role),
                            datasets: [{
                                label: 'Total Users',
                                data: usersByRoleData.map(d => d.count),
                                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                        }
                    });
                }

                // 2. Chart: Status Verifikasi Pengguna (User Verification Status)
                const userVerificationCtx = document.getElementById('userVerificationStatusChart');
                if (userVerificationCtx) {
                    // This code now works because the controller sends a proper array
                    const userVerificationData = @json($userVerificationStatus ?? []);
                    new Chart(userVerificationCtx, {
                        type: 'doughnut',
                        data: {
                            labels: userVerificationData.map(d => d.status), // This will now work
                            datasets: [{
                                label: 'Users',
                                data: userVerificationData.map(d => d.count), // This will also work
                                backgroundColor: ['#1cc88a', '#f6c23e'],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                        }
                    });
                }

                // 3. Chart: Tren Pendaftaran Pengguna Baru (User Registration Trends)
                const userRegistrationCtx = document.getElementById('userRegistrationTrendsChart');
                if (userRegistrationCtx) {
                    const userRegistrationData = @json($userRegistrationTrends ?? []);
                    new Chart(userRegistrationCtx, {
                        type: 'line',
                        data: {
                            labels: userRegistrationData.map(d => d.date),
                            datasets: [{
                                label: 'New Users',
                                data: userRegistrationData.map(d => d.count),
                                borderColor: '#4e73df',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                // 4. Chart: Status Kursus (Courses by Status)
                const courseStatusCtx = document.getElementById('courseStatusChart');
                if (courseStatusCtx) {
                    const courseStatusData = @json($courseStatusData ?? []);
                    new Chart(courseStatusCtx, {
                        type: 'bar',
                        data: {
                            labels: courseStatusData.map(d => d.status),
                            datasets: [{
                                label: 'Total Courses',
                                data: courseStatusData.map(d => d.count),
                                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e'],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
                
                // 5. Chart: Siswa Aktif Harian (Daily Active Students)
                const dailyActiveStudentsCtx = document.getElementById('dailyActiveStudentsChart');
                if (dailyActiveStudentsCtx) {
                    const dasLabels = @json($dasLabels ?? []);
                    const dasData = @json($dasData ?? []);
                    new Chart(dailyActiveStudentsCtx, {
                        type: 'line',
                        data: {
                            labels: dasLabels,
                            datasets: [{
                                label: 'Active Students',
                                data: dasData,
                                borderColor: '#1cc88a',
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

            });
        </script>
    @endpush
</x-app-layout>