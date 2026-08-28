<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lembar Monitoring Guru Pembimbing
            </h2>
            <a href="{{ route('teacher.journal.show', $journal->id) }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('teacher.update-monitoring', $journal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <p class="text-sm text-gray-600 mb-6">Berikan tanda centang pada kolom <strong>Ya</strong> atau <strong>Tidak</strong> sesuai hasil monitoring pelaksanaan PKL siswa.</p>

                    <div class="space-y-4">
                        @foreach($monitoringAssessments as $index => $item)
                        <div class="p-4 bg-gray-50 rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border border-gray-200">
                            <span class="text-sm text-gray-800 font-medium">
                                {{ $index + 1 }}. {{ $item->name }}
                            </span>
                            
                            <div class="flex items-center space-x-6 shrink-0">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="monitoring[{{ $item->id }}]" value="1" 
                                        {{ old("monitoring.{$item->id}", optional($existingAnswers[$item->id] ?? null)->is_yes) === true ? 'checked' : '' }} 
                                        class="text-indigo-600 focus:ring-indigo-500" required>
                                    <span class="ml-2 text-sm text-gray-700 font-semibold">Ya</span>
                                </label>

                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="monitoring[{{ $item->id }}]" value="0" 
                                        {{ old("monitoring.{$item->id}", optional($existingAnswers[$item->id] ?? null)->is_yes) === false ? 'checked' : '' }} 
                                        class="text-indigo-600 focus:ring-indigo-500" required>
                                    <span class="ml-2 text-sm text-gray-700 font-semibold">Tidak</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t">
                        <x-primary-button>
                            Simpan Lembar Monitoring
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>