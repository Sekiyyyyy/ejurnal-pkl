<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-800 tracking-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="space-y-6">

        <!-- Clean Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md hover:border-blue-200 transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Siswa</p>
                    <h4 class="text-2xl font-black text-slate-800">{{ $totalStudents }}</h4>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md hover:border-emerald-200 transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Jurusan</p>
                    <h4 class="text-2xl font-black text-slate-800">{{ $totalMajors }}</h4>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md hover:border-amber-200 transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Jurnal Aktif</p>
                    <h4 class="text-2xl font-black text-slate-800">{{ $totalJournals }}</h4>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md hover:border-indigo-200 transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-file-contract"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Template</p>
                    <h4 class="text-2xl font-black text-slate-800">{{ $totalTemplates }}</h4>
                </div>
            </div>
        </div>

        <!-- Lower Section Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Chart Section -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 flex flex-col hover:shadow-md transition-shadow duration-300">
                <h4 class="text-sm font-bold text-slate-800 mb-6 uppercase tracking-wider">Distribusi Siswa</h4>
                
                <div class="flex-1 flex flex-col items-center justify-center relative min-h-[300px]">
                    @if(empty($chartLabels) || empty($chartData))
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="fa-solid fa-chart-pie text-4xl mb-2 opacity-30"></i>
                            <p class="text-sm">Belum ada data distribusi.</p>
                        </div>
                    @else
                        <!-- Chart Container -->
                        <div class="relative w-full max-w-[280px] h-[280px]">
                            <canvas id="majorChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Users Section -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm flex flex-col hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                    <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Pendaftar Terbaru</h4>
                    <a href="{{ route('admin.students.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-1 rounded">
                        Lihat Semua &rarr;
                    </a>
                </div>
                
                <div class="flex-1 p-0">
                    <ul class="divide-y divide-slate-100">
                        @forelse($recentStudents as $student)
                            <li class="flex items-center gap-4 p-4 hover:bg-slate-50 transition-colors group">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold border border-indigo-100 shrink-0 group-hover:scale-105 transition-transform">
                                    {{ substr($student->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-bold text-slate-800 truncate group-hover:text-indigo-600 transition-colors">{{ $student->name }}</h5>
                                    <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                                        <span class="font-medium bg-slate-100 px-1.5 py-0.5 rounded">{{ $student->nisn }}</span>
                                        <span>&bull;</span>
                                        <span class="truncate">{{ $student->major->code }}</span>
                                    </div>
                                </div>
                                <div class="text-[10px] font-bold text-slate-400 whitespace-nowrap uppercase tracking-wider">
                                    {{ $student->created_at->diffForHumans() }}
                                </div>
                            </li>
                        @empty
                            <li class="flex flex-col items-center justify-center py-10 text-slate-400">
                                <p class="text-sm">Belum ada pendaftar.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Chart.js Script -->
    @if(!empty($chartLabels) && !empty($chartData))
        @php
            $colors = [
                '#3b82f6', // Blue
                '#10b981', // Emerald
                '#f59e0b', // Amber
                '#6366f1', // Indigo
                '#ec4899', // Pink
            ];
            
            // Repeat colors if needed
            $backgroundColors = array_slice(array_pad([], count($chartData), ''), 0, count($chartData));
            foreach ($backgroundColors as $index => &$color) {
                $color = $colors[$index % count($colors)];
            }
        @endphp

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const ctx = document.getElementById('majorChart');
                
                if (ctx) {
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($chartLabels) !!},
                            datasets: [{
                                data: {!! json_encode($chartData) !!},
                                backgroundColor: {!! json_encode($backgroundColors) !!},
                                borderWidth: 2,
                                borderColor: '#ffffff',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        usePointStyle: true,
                                        font: {
                                            family: "'Inter', sans-serif",
                                            size: 12
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</x-admin-layout>