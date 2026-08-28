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
                        
                        <!-- Tempat PKL -->
                        <div>
                            <x-input-label for="company_id" value="Tempat PKL" />
                            <select name="company_id" id="company_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Tempat PKL --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id', $journal->company_id) == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Guru Pembimbing -->
                        <div>
                            <x-input-label for="teacher_id" value="Guru Pembimbing" />
                            <select name="teacher_id" id="teacher_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Guru Pembimbing --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $journal->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
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