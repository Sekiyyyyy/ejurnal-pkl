<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Akun Siswa') }}
            </h2>
            <a href="{{ route('admin.students.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                + Tambah Siswa Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600 border">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 border text-center">No</th>
                                <th class="px-4 py-3 border">NISN</th>
                                <th class="px-4 py-3 border">Nama Siswa</th>
                                <th class="px-4 py-3 border">Jurusan</th>
                                <th class="px-4 py-3 border">Email Login</th>
                                <th class="px-4 py-3 border text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 border text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 border font-semibold">{{ $student->nisn }}</td>
                                <td class="px-4 py-3 border">{{ $student->name }}</td>
                                <td class="px-4 py-3 border">{{ $student->major->code ?? '-' }}</td>
                                <td class="px-4 py-3 border">{{ optional($student->user)->email ?? '-' }}</td>
                                <td class="px-4 py-3 border text-center">
                                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Peringatan: Menghapus siswa akan menghapus seluruh data jurnalnya. Lanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-xs bg-red-100 px-2 py-1 rounded">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">Belum ada akun siswa yang didaftarkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>