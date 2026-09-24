<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Akun Siswa</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success')) 
                    <x-alert type="success" :message="session('success')" class="mb-4" /> 
                @endif
                @if($errors->any()) 
                    <x-alert type="error" :message="$errors->first()" class="mb-4" /> 
                @endif

                <!-- Search & Filters -->
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 text-sm font-semibold rounded {{ request('filter') != 'expired' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Semua Siswa
                        </a>
                        <a href="{{ route('admin.students.index', ['filter' => 'expired']) }}" class="px-4 py-2 text-sm font-semibold rounded {{ request('filter') == 'expired' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Siap Dihapus (Expired)
                        </a>
                    </div>
                    
                    <form action="{{ route('admin.students.index') }}" method="GET" class="w-full sm:w-1/3">
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NISN, atau Email..." class="w-full border-gray-300 rounded-md shadow-sm pl-4 pr-10 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit" class="absolute right-0 top-0 mt-2 mr-3 text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">Nama Lengkap</th>
                                <th class="px-4 py-3">NISN / Email</th>
                                <th class="px-4 py-3">Jurusan</th>
                                <th class="px-4 py-3">Tgl Daftar</th>
                                <th class="px-4 py-3">Status Selesai</th>
                                <th class="px-4 py-3 text-center">Aksi (Admin)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold text-gray-900">
                                    <div>{{ $student->name }}</div>
                                    @if($student->phone)
                                        @php
                                            $cleanP = preg_replace('/[^0-9]/', '', $student->phone);
                                            if (str_starts_with($cleanP, '0')) $cleanP = '62' . substr($cleanP, 1);
                                        @endphp
                                        <div class="mt-1">
                                            <a href="https://wa.me/{{ $cleanP }}?text={{ urlencode('Halo ' . $student->name . ', saya dari Admin/Guru SMKN 1 Beringin terkait jurnal PKL...') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 font-semibold bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200 transition">
                                                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                                <span>{{ $student->phone }}</span>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-mono text-gray-800">{{ $student->nisn }}</div>
                                    <div class="text-xs text-gray-400">{{ $student->user->email ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $student->major->name ?? 'Belum ada jurusan' }}</td>
                                <td class="px-4 py-3">{{ $student->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($student->jurnal_2_completed_at)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded font-semibold">
                                            Selesai PKL 2 ({{ \Carbon\Carbon::parse($student->jurnal_2_completed_at)->diffForHumans() }})
                                        </span>
                                    @elseif($student->jurnal_1_completed_at)
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded font-semibold">
                                            Selesai PKL 1 ({{ \Carbon\Carbon::parse($student->jurnal_1_completed_at)->diffForHumans() }})
                                        </span>
                                    @else
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Belum Selesai</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 flex flex-wrap justify-center items-center gap-1.5">
                                    
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1 rounded text-xs font-bold transition">
                                        Detail
                                    </a>

                                    @if($student->user)
                                        <!-- Tombol Login Sebagai Siswa -->
                                        <form action="{{ route('admin.students.impersonate', $student->id, false) }}" method="POST" onsubmit="return confirm('Masuk ke aplikasi sebagai siswa {{ $student->name }}?');">
                                            @csrf
                                            <button type="submit" class="bg-purple-100 text-purple-700 hover:bg-purple-200 px-2.5 py-1 rounded text-xs font-bold transition flex items-center gap-1" title="Login Sebagai Siswa">
                                                <i class="fa-solid fa-right-to-bracket text-xs"></i>
                                                <span>Login</span>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <!-- Tombol Reset Password -->
                                    <form action="{{ route('admin.students.reset-password', $student->id, false) }}" method="POST" onsubmit="return confirm('Reset password akun ini menjadi: password123 ?');">
                                        @csrf @method('PUT')
                                        <button type="submit" class="bg-yellow-100 text-yellow-800 hover:bg-yellow-200 px-2.5 py-1 rounded text-xs font-bold transition">
                                            Reset PW
                                        </button>
                                    </form>

                                    <!-- Tombol Hapus Akun -->
                                    <form action="{{ route('admin.students.destroy', $student->id, false) }}" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus siswa ini juga akan menghapus seluruh data jurnal, absen, dan kegiatannya secara permanen. Yakin?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-2.5 py-1 rounded text-xs font-bold transition">
                                            Hapus
                                        </button>
                                    </form>
                                    
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($students->hasPages())
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>