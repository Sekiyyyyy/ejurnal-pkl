<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lembar Monitoring Guru Pembimbing (Fase {{ $journal->phase }})
            </h2>
            <a href="{{ route('journal.show', $journal->id) }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if($journal->monitoring_locked_at)
                    <!-- READ-ONLY STATE -->
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded text-sm text-green-800 flex items-start gap-3">
                        <svg class="h-6 w-6 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div>
                            <strong>Telah Dikunci (Read-Only)</strong>
                            <p class="mt-1">Form monitoring ini telah diisi dan divalidasi oleh Guru Pembimbing pada <strong>{{ $journal->monitoring_locked_at->translatedFormat('d F Y, H:i') }}</strong>. Data ini sudah terkunci dan tidak dapat diubah.</p>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        @foreach($monitoringAssessments as $index => $item)
                        <div class="p-4 bg-gray-50 rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border border-gray-200 opacity-90">
                            <span class="text-sm text-gray-800 font-medium">
                                {{ $index + 1 }}. {{ $item->name }}
                            </span>
                            <div class="shrink-0">
                                @if(optional($existingAnswers[$item->id] ?? null)->is_yes)
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded font-bold text-xs uppercase">Ya</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded font-bold text-xs uppercase">Tidak</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Bukti Otorisasi Guru Pembimbing</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-200">
                        <!-- TTD Read-Only -->
                        <div>
                            <p class="text-sm font-bold text-gray-500 uppercase mb-2">Tanda Tangan</p>
                            <div class="h-32 bg-white border border-gray-200 rounded-lg flex items-center justify-center p-2">
                                @if($journal->teacher_signature)
                                    <img src="{{ asset('storage/' . $journal->teacher_signature) }}" class="max-h-full max-w-full object-contain" alt="TTD Guru">
                                @else
                                    <span class="text-gray-400 text-xs italic">Tidak ada tanda tangan</span>
                                @endif
                            </div>
                        </div>
                        <!-- Live Photo Read-Only -->
                        <div>
                            <p class="text-sm font-bold text-gray-500 uppercase mb-2">Foto Live Wajah</p>
                            <div class="h-32 bg-white border border-gray-200 rounded-lg flex items-center justify-center p-2 overflow-hidden">
                                @if($journal->teacher_live_photo)
                                    <img src="{{ asset('storage/' . $journal->teacher_live_photo) }}" class="max-h-full w-auto object-cover rounded" alt="Live Photo Guru">
                                @else
                                    <span class="text-gray-400 text-xs italic">Tidak ada foto</span>
                                @endif
                            </div>
                        </div>
                    </div>

                @else
                    <!-- EDITABLE STATE -->
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                        <strong>Pemberitahuan Device Handoff:</strong> Halaman ini harus diisi secara langsung oleh <strong>Guru Pembimbing</strong> dengan menggunakan perangkat (HP/Laptop) milik siswa ini.
                    </div>

                    <form id="form-monitoring" action="{{ route('journal.update-monitoring', $journal->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4 mb-8">
                            @foreach($monitoringAssessments as $index => $item)
                            <div class="p-4 bg-gray-50 rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border border-gray-200">
                                <span class="text-sm text-gray-800 font-medium">
                                    {{ $index + 1 }}. {{ $item->name }}
                                </span>
                                
                                <div class="flex items-center space-x-6 shrink-0">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="monitoring[{{ $item->id }}]" value="1" required
                                            {{ old("monitoring.{$item->id}", optional($existingAnswers[$item->id] ?? null)->is_yes) == '1' ? 'checked' : '' }} 
                                            class="text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                                        <span class="ml-2 text-sm text-gray-700 font-bold">Ya</span>
                                    </label>

                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="monitoring[{{ $item->id }}]" value="0" required
                                            {{ old("monitoring.{$item->id}", optional($existingAnswers[$item->id] ?? null)->is_yes) == '0' ? 'checked' : '' }} 
                                            class="text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                                        <span class="ml-2 text-sm text-gray-700 font-bold">Tidak</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Live Authenticator Component for Signature & Photo -->
                        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6">
                            <p class="text-sm text-indigo-800 font-medium">
                                <strong>Verifikasi Guru:</strong> Silakan bubuhkan tanda tangan dan ambil foto wajah secara langsung (Live Camera) sebagai bukti sah pengisian instrumen. Form akan terkunci permanen setelah disubmit.
                            </p>
                        </div>
                        <x-live-authenticator idPrefix="monitoring-auth" formId="form-monitoring" signatureLabel="Tanda Tangan" cameraHelperText="Wajib ambil foto wajah secara live BERSAMA guru pembimbing." />

                        <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                                Kunci & Simpan Permanen
                            </x-primary-button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>