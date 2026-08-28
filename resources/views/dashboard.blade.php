<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                @foreach($journals as $journal)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Jurnal PKL Fase {{ $journal->phase }}</h3>
                    <p class="text-sm text-gray-600 mb-4">Status: 
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                            {{ $journal->status }}
                        </span>
                    </p>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-sm text-gray-700 mb-1"><strong>Tempat PKL:</strong> {{ $journal->company->name ?? 'Belum dipilih' }}</p>
                        <p class="text-sm text-gray-700 mb-4"><strong>Pembimbing:</strong> {{ $journal->teacher->name ?? 'Belum dipilih' }}</p>
                        
                        <!-- BAGIAN TOMBOL INI YANG TERLEWAT -->
                        <a href="{{ route('journal.show', $journal->id) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                            Buka Jurnal
                        </a>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</x-app-layout>