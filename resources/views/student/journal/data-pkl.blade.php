<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Isi Data PKL (Fase {{ $journal->phase }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('journal.update-data-pkl', $journal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Tempat PKL & Alamat -->
                        <div>
                            <x-input-label for="company_name" value="Nama Tempat PKL" />
                            <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name', $journal->company_name)" required />
                        </div>
                        <div>
                            <x-input-label for="company_address" value="Alamat Tempat PKL" />
                            <x-text-input id="company_address" class="block mt-1 w-full" type="text" name="company_address" :value="old('company_address', $journal->company_address)" required />
                        </div>

                        <!-- Guru Pembimbing -->
                        <div>
                            <x-input-label for="teacher_name" value="Nama Guru Pembimbing (Beserta Gelar)" />
                            <x-text-input id="teacher_name" class="block mt-1 w-full" type="text" name="teacher_name" :value="old('teacher_name', $journal->teacher_name)" placeholder="Misal: Budi Guru, S.Kom" required />
                        </div>

                        <!-- Data Instruktur -->
                        <div>
                            <x-input-label for="instructor_name" value="Nama Instruktur / Penanggung Jawab" />
                            <x-text-input id="instructor_name" class="block mt-1 w-full" type="text" name="instructor_name" :value="old('instructor_name', $journal->instructor_name)" />
                        </div>

                        <div>
                            <x-input-label for="instructor_position" value="Jabatan Instruktur" />
                            <x-text-input id="instructor_position" class="block mt-1 w-full" type="text" name="instructor_position" :value="old('instructor_position', $journal->instructor_position)" />
                        </div>

                        <div>
                            <x-input-label for="instructor_phone" value="No. HP/WA Instruktur" />
                            <x-text-input id="instructor_phone" class="block mt-1 w-full" type="text" name="instructor_phone" :value="old('instructor_phone', $journal->instructor_phone)" />
                        </div>

                        <!-- Periode PKL -->
                        <div>
                            <x-input-label for="start_date" value="Tanggal Mulai PKL" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $journal->start_date ? $journal->start_date->format('Y-m-d') : '')" />
                        </div>

                        <div>
                            <x-input-label for="end_date" value="Tanggal Selesai PKL" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date', $journal->end_date ? $journal->end_date->format('Y-m-d') : '')" />
                        </div>

                    </div>

                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('journal.show', $journal->id) }}" class="text-gray-600 hover:underline">Batal</a>
                        <x-primary-button>
                            Simpan Progress
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>