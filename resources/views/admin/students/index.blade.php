<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Akun Siswa</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success')) <div class="mb-4 text-sm text-green-600 bg-green-50 p-3 rounded font-medium">{{ session('success') }}</div> @endif
                @if($errors->any()) <div class="mb-4 text-sm text-red-600 bg-red-50 p-3 rounded font-medium">{{ $errors->first() }}</div> @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">Nama Lengkap</th>
                                <th class="px-4 py-3">NISN</th>
                                <th class="px-4 py-3">Jurusan</th>
                                <th class="px-4 py-3 text-center">Aksi (Admin)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold text-gray-900">{{ $student->name }}</td>
                                <td class="px-4 py-3">{{ $student->nisn }}</td>
                                <td class="px-4 py-3">{{ $student->major->name ?? 'Belum ada jurusan' }}</td>
                                <td class="px-4 py-3 flex justify-center gap-2">
                                    
                                    <!-- Tombol Reset Password -->
                                    <form action="{{ route('admin.students.reset-password', $student->id) }}" method="POST" onsubmit="return confirm('Reset password akun ini menjadi: password123 ?');">
                                        @csrf @method('PUT')
                                        <button type="submit" class="bg-yellow-100 text-yellow-800 hover:bg-yellow-200 px-3 py-1 rounded text-xs font-bold transition">
                                            Reset Password
                                        </button>
                                    </form>

                                    <!-- Tombol Hapus Akun -->
                                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus siswa ini juga akan menghapus seluruh data jurnal, absen, dan kegiatannya secara permanen. Yakin?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded text-xs font-bold transition">
                                            Hapus Akun
                                        </button>
                                    </form>
                                    
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>