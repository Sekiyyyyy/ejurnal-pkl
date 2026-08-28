<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Guru Pembimbing
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-lg text-gray-800 mb-4">Daftar Siswa Bimbingan</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3">Nama Siswa & NISN</th>
                                <th class="px-4 py-3">Jurusan & Kelas</th>
                                <th class="px-4 py-3">Tempat PKL</th>
                                <th class="px-4 py-3">Fase</th>
                                <th class="px-4 py-3">Status Jurnal</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($journals as $journal)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $journal->student->name }}
                                    <span class="block text-xs text-gray-500">{{ $journal->student->nisn }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $journal->student->major->code ?? '-' }}
                                    <span class="block text-xs text-gray-500">{{ $journal->student->class ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $journal->company->name ?? 'Belum ada' }}</td>
                                <td class="px-4 py-3 font-semibold">PKL Fase {{ $journal->phase }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                        {{ $journal->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('teacher.journal.show', $journal->id) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-3 rounded text-xs transition">
                                        Periksa Jurnal
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada siswa yang ditugaskan kepada Anda.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>