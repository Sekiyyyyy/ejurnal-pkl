<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Minta ACC Mingguan (Minggu ke-' . $weekNumber . ')') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Rekap Kegiatan Minggu ke-{{ $weekNumber }} Tahun {{ $year }}</h3>
                    
                    <div class="overflow-x-auto mb-6">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Tanggal</th>
                                    <th scope="col" class="px-4 py-3">Waktu</th>
                                    <th scope="col" class="px-4 py-3">Divisi</th>
                                    <th scope="col" class="px-4 py-3">Kegiatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activitiesToApprove as $activity)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($activity->date)->translatedFormat('l, d F Y') }}</td>
                                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($activity->end_time)->format('H:i') }}</td>
                                        <td class="px-4 py-3">{{ $activity->division ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $activity->activity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Pemberitahuan Device Handoff:</strong> Halaman ini harus diisi secara langsung oleh <strong>Instruktur DUDI</strong> dengan menggunakan perangkat (HP/Laptop) milik siswa ini untuk melakukan verifikasi, validasi foto live, dan tanda tangan (paraf).
                                </p>
                            </div>
                        </div>
                    </div>

                    <form id="form-weekly-approval" action="{{ route('journal.weekly-approval.store', $journal->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="week_number" value="{{ $weekNumber }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        
                        <x-live-authenticator idPrefix="week-{{ $weekNumber }}" formId="form-weekly-approval" cameraHelperText="Wajib ambil foto wajah secara live BERSAMA instruktur PKL." />
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('journal.show', $journal->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Setujui & Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
