<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Super Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <h3 class="text-lg font-bold text-gray-800">Selamat datang, {{ Auth::user()->name }}!</h3>
                <p class="text-sm text-gray-500">Anda mengelola sistem e-Jurnal PKL secara penuh.</p>
            </div>

            <!-- Card Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-indigo-600 text-white rounded-lg p-6 shadow">
                    <h4 class="text-indigo-100 text-sm font-semibold mb-1">Total Siswa</h4>
                    <p class="text-3xl font-bold">{{ $totalStudents }}</p>
                </div>
                
                <div class="bg-emerald-600 text-white rounded-lg p-6 shadow">
                    <h4 class="text-emerald-100 text-sm font-semibold mb-1">Total Jurnal (Fase 1 & 2)</h4>
                    <p class="text-3xl font-bold">{{ $totalJournals }}</p>
                </div>

                <div class="bg-blue-600 text-white rounded-lg p-6 shadow">
                    <h4 class="text-blue-100 text-sm font-semibold mb-1">Jurnal Selesai (100%)</h4>
                    <p class="text-3xl font-bold">{{ $completedJournals }}</p>
                </div>

                <div class="bg-purple-600 text-white rounded-lg p-6 shadow">
                    <h4 class="text-purple-100 text-sm font-semibold mb-1">Total Jurusan</h4>
                    <p class="text-3xl font-bold">{{ $totalMajors }}</p>
                </div>
            </div>
            
            <div class="mt-8 text-center text-gray-500 text-sm">
                <!-- Nanti di sini kita tambah tabel menu pintasan -->
                <p>Fitur Manajemen Siswa & Rekap Jurnal akan diletakkan di sini.</p>
            </div>

        </div>
    </div>
</x-app-layout>