<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Administrator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Pesan Selamat Datang -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4 border-indigo-500">
                <div class="p-6 text-gray-900 font-semibold text-lg">
                    Selamat datang, Super Admin!
                </div>
            </div>

            <!-- 4 Card Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                
                <!-- Card Total Siswa -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Total Siswa</p>
                            <h3 class="text-3xl font-bold mt-1">{{ $totalStudents }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Card Total Jurusan -->
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium uppercase tracking-wider">Total Jurusan</p>
                            <h3 class="text-3xl font-bold mt-1">{{ $totalMajors }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Card Total Jurnal -->
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-amber-100 text-sm font-medium uppercase tracking-wider">Total Jurnal PKL</p>
                            <h3 class="text-3xl font-bold mt-1">{{ $totalJournals }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Card Total Template -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-purple-100 text-sm font-medium uppercase tracking-wider">Template Sistem</p>
                            <h3 class="text-3xl font-bold mt-1">{{ $totalTemplates }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bagian Grid untuk Grafik dan Informasi -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Kiri: Grafik Chart.js -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Distribusi Siswa Berdasarkan Jurusan</h3>
                    <!-- Area Canvas untuk Grafik -->
                    <div class="relative h-64 w-full">
                        <canvas id="studentsChart"></canvas>
                    </div>
                </div>
                
                <!-- Kanan: Siswa Mendaftar Terbaru -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="font-bold text-gray-800">Pendaftar Terbaru</h3>
                        <a href="{{ route('admin.students.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold hover:underline">Lihat Semua &rarr;</a>
                    </div>
                    
                    @if($recentStudents->isEmpty())
                        <p class="text-sm text-gray-500 italic text-center py-6">Belum ada data pendaftar.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentStudents as $student)
                            <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="bg-indigo-100 text-indigo-600 p-2 rounded-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $student->name }}</p>
                                        <p class="text-xs text-gray-500">NISN: {{ $student->nisn }} • {{ $student->major->code ?? 'Tanpa Jurusan' }}</p>
                                    </div>
                                </div>
                                <div class="text-[11px] text-gray-400 font-medium text-right">
                                    {{ $student->created_at->diffForHumans() }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('studentsChart').getContext('2d');
            
            // Mengambil data dari Controller
            const chartLabels = {!! json_encode($chartLabels) !!};
            const chartData = {!! json_encode($chartData) !!};

            new Chart(ctx, {
                type: 'doughnut', // Tipe grafik cincin
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah Siswa',
                        data: chartData,
                        backgroundColor: [
                            '#3b82f6', // Biru
                            '#10b981', // Hijau
                            '#f59e0b', // Oranye
                            '#8b5cf6', // Ungu
                            '#ec4899', // Pink
                            '#14b8a6', // Teal
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>