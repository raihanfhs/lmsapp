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
                    <h3 class="text-lg font-medium mb-4">Total Users by Role</h3>
                    {{-- Sama seperti di artikel, kita siapkan canvas untuk chart --}}
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Memuat library Chart.js dari CDN, persis seperti di artikel --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Mengambil data yang dikirim dari controller
        const rawData = @json($usersByRole);

        // --- Mulai bagian yang sama persis dengan pola di artikel ---

        // Menyiapkan data untuk Chart.js
        const data = {
            labels: rawData.map(item => item.role.charAt(0).toUpperCase() + item.role.slice(1)), // Membuat nama role menjadi Title Case
            datasets: [{
                label: 'Total Users',
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgb(54, 162, 235)',
                data: rawData.map(item => item.count),
            }]
        };

        // Konfigurasi chart
        const config = {
            type: 'bar', // Tipe chart adalah 'bar'
            data: data,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        };

        // Membuat chart baru
        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );

        // --- Selesai bagian pola artikel ---
    </script>
    @endpush
</x-app-layout>
