<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alert jika ada error akses dari Controller -->
            @if($errors->has('access'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Peringatan</p>
                    <p>{{ $errors->first('access') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                @php
                    // Cek apakah jurnal fase 1 sudah berstatus selesai
                    $fase1Completed = $journals->where('phase', 1)->whereIn('status', ['COMPLETED', 'READY_TO_GENERATE', 'GENERATED'])->isNotEmpty();
                @endphp

                @foreach($journals as $journal)
                @php
                    // Logika Kunci: Jurnal terkunci JIKA ini Fase 2 DAN Fase 1 belum selesai
                    $isLocked = ($journal->phase == 2 && !$fase1Completed);
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 relative {{ $isLocked ? 'opacity-80' : '' }}">
                    
                    @if($isLocked)
                        <!-- Overlay Gembok -->
                        <div class="absolute top-4 right-4 text-red-500">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        </div>
                    @endif

                    <h3 class="text-lg font-bold text-gray-900 mb-2">Jurnal PKL Fase {{ $journal->phase }}</h3>
                    <p class="text-sm text-gray-600 mb-4">Status: 
                        <span class="px-2 py-1 rounded text-xs font-semibold 
                            {{ $journal->status == 'DRAFT' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $journal->status }}
                        </span>
                    </p>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <!-- PERBAIKAN DI SINI -->
                        <p class="text-sm text-gray-700 mb-1"><strong>Tempat PKL:</strong> {{ $journal->company_name ?? 'Belum dipilih' }}</p>
                        <p class="text-sm text-gray-700 mb-4"><strong>Pembimbing:</strong> {{ $journal->teacher_name ?? 'Belum dipilih' }}</p>
                        
                        @if($isLocked)
                            <button disabled class="inline-block bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                                Terkunci (Selesaikan Fase 1)
                            </button>
                        @else
                            <a href="{{ route('journal.show', $journal->id) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                                Buka Jurnal
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</x-app-layout>