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
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Perhatian Instruktur:</strong> Dengan memberikan paraf dan foto live di bawah ini, Anda menyetujui seluruh logbook siswa untuk minggu ini secara bersamaan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form id="form-weekly-approval" action="{{ route('journal.weekly-approval.store', $journal->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="week_number" value="{{ $weekNumber }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        
                        <x-live-authenticator idPrefix="week-{{ $weekNumber }}" formId="form-weekly-approval" />
                        
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
