<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Catatan Harian (Fase {{ $journal->phase }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if($errors->any())
                    <div class="bg-red-100 text-red-700 p-2 rounded text-sm mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('journal.update-activity', [$journal->id, $activity->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <x-input-label for="date" value="Tanggal" />
                        <x-text-input id="date" class="block mt-1 w-full bg-gray-100" type="date" name="date" value="{{ $activity->date->format('Y-m-d') }}" readonly />
                    </div>

                    <div class="mb-3">
                        <x-input-label for="status" value="Status Kehadiran" />
                        <select name="status" id="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="Hadir" {{ $activity->status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="Sakit" {{ $activity->status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="Izin" {{ $activity->status == 'Izin' ? 'selected' : '' }}>Izin</option>
                            <option value="Alpa" {{ $activity->status == 'Alpa' ? 'selected' : '' }}>Alpa</option>
                            <option value="Libur" {{ $activity->status == 'Libur' ? 'selected' : '' }}>Libur</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <x-input-label for="start_time" value="Jam Mulai" />
                            <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" value="{{ $activity->start_time ? \Carbon\Carbon::parse($activity->start_time)->format('H:i') : '' }}" />
                        </div>
                        <div>
                            <x-input-label for="end_time" value="Jam Selesai" />
                            <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" value="{{ $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : '' }}" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="division" value="Divisi / Departemen" />
                        <x-text-input id="division" class="block mt-1 w-full" type="text" name="division" value="{{ $activity->division }}" />
                    </div>

                    <div class="mb-3">
                        <x-input-label for="activity" value="Uraian Pekerjaan / Aktivitas" />
                        <textarea id="activity" name="activity" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ $activity->activity }}</textarea>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="character_values" value="Nilai Karakter Budaya Kerja" />
                        <x-text-input id="character_values" class="block mt-1 w-full" type="text" name="character_values" value="{{ $activity->character_values }}" />
                    </div>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('journal.activity', $journal->id) }}" class="text-gray-600 hover:underline text-sm">Batal</a>
                        <x-primary-button>Perbarui Catatan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>