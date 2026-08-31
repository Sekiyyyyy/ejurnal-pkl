<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrasi Akun Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.students.store') }}" method="POST">
                    @csrf
                    
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">A. Data Akademik</h3>
                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <div>
                            <x-input-label for="nisn" value="NISN (Nomor Induk Siswa Nasional)" />
                            <x-text-input id="nisn" class="block mt-1 w-full" type="text" name="nisn" value="{{ old('nisn') }}" required autofocus />
                        </div>
                        <div>
                            <x-input-label for="name" value="Nama Lengkap Siswa" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name') }}" required />
                        </div>
                        <div>
                            <x-input-label for="major_id" value="Program Keahlian (Jurusan)" />
                            <select id="major_id" name="major_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($majors as $major)
                                    <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                        {{ $major->code }} - {{ $major->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">B. Akun Login</h3>
                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <div>
                            <x-input-label for="email" value="Email Siswa" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email') }}" required />
                        </div>
                        <div>
                            <x-input-label for="password" value="Password (Minimal 8 Karakter)" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <a href="{{ route('admin.students.index') }}" class="mr-3 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Batal</a>
                        <x-primary-button>Simpan & Buat Akun</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>