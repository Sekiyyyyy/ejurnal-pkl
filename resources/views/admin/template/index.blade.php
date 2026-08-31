<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Template Jurnal (Word)') }}
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
                
                <!-- Form Upload Template -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Upload Template Baru</h3>
                    <div class="text-sm text-gray-500 mb-4">
                        Pastikan file berformat <strong>.docx</strong> dan memiliki parameter mapping yang tepat (misal: <code>${siswa_nama}</code>).
                    </div>
                    <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="name" value="Nama / Versi Template" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" placeholder="Misal: Format Jurnal 2026" required />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="file" value="File Dokumen (.docx)" />
                            <input id="file" type="file" name="file" accept=".docx" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required />
                        </div>
                        <x-primary-button class="w-full justify-center">Upload Template</x-primary-button>
                    </form>
                </div>

                <!-- Tabel Daftar Template -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Daftar Template Tersedia</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 border">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 border">Nama Template</th>
                                    <th class="px-4 py-3 border text-center">Status</th>
                                    <th class="px-4 py-3 border text-center w-48">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 border font-medium text-gray-900">{{ $template->name }}</td>
                                    <td class="px-4 py-3 border text-center">
                                        @if($template->is_active)
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded">AKTIF DIGUNAKAN</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">TIDAK AKTIF</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border flex justify-center gap-2">
                                        @if(!$template->is_active)
                                            <form action="{{ route('admin.templates.activate', $template->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 font-semibold text-xs px-2 py-1 rounded">Gunakan Ini</button>
                                            </form>
                                            <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Hapus template ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-xs bg-red-100 px-2 py-1 rounded">Hapus</button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Sedang Dipakai</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-center text-gray-500">Belum ada template. Silakan upload terlebih dahulu.</td>
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