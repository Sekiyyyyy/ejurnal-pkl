<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Jurusan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Kolom Tambah Jurusan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Tambah Jurusan Baru</h3>
                    <form action="{{ route('admin.majors.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="code" value="Kode / Singkatan" />
                            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" placeholder="Misal: PPLG" required autofocus />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="name" value="Nama Program Keahlian" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" placeholder="Misal: Pengembangan Perangkat Lunak dan Gim" required />
                        </div>
                        <x-primary-button class="w-full justify-center">Simpan Jurusan</x-primary-button>
                    </form>
                </div>

                <!-- Kolom Tabel Daftar Jurusan -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Daftar Jurusan</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 border">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 border w-10 text-center">No</th>
                                    <th class="px-4 py-3 border w-24">Kode</th>
                                    <th class="px-4 py-3 border">Nama Jurusan</th>
                                    <th class="px-4 py-3 border w-24 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($majors as $index => $major)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 border text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 border font-bold text-gray-800">{{ $major->code }}</td>
                                    <td class="px-4 py-3 border font-medium text-gray-900">{{ $major->name }}</td>
                                    <td class="px-4 py-3 border text-center">
                                        <form action="{{ route('admin.majors.destroy', $major->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-xs bg-red-100 px-2 py-1 rounded">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-gray-500">Belum ada data jurusan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>