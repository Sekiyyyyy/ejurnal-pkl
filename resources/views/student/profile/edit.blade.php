<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lengkapi Biodata Diri & Orang Tua
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('student.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- DATA SISWA -->
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">A. Data Siswa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <x-input-label value="Nama Lengkap" />
                            <x-text-input type="text" class="block mt-1 w-full bg-gray-100" value="{{ $student->name }}" disabled />
                        </div>
                        <div>
                            <x-input-label value="NISN" />
                            <x-text-input type="text" class="block mt-1 w-full bg-gray-100" value="{{ $student->nisn }}" disabled />
                        </div>
                        <div>
                            <x-input-label for="class" value="Kelas (Misal: XII TKJ 1)" />
                            <x-text-input id="class" name="class" type="text" class="block mt-1 w-full" value="{{ old('class', $student->class) }}" required />
                        </div>
                        <div>
                            <x-input-label for="gender" value="Jenis Kelamin" />
                            <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('gender', $student->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender', $student->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="birth_place" value="Tempat Lahir" />
                            <x-text-input id="birth_place" name="birth_place" type="text" class="block mt-1 w-full" value="{{ old('birth_place', $student->birth_place) }}" required />
                        </div>
                        <div>
                            <x-input-label for="birth_date" value="Tanggal Lahir" />
                            <x-text-input id="birth_date" name="birth_date" type="date" class="block mt-1 w-full" value="{{ old('birth_date', $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('Y-m-d') : '') }}" required />
                        </div>
                        <div>
                            <x-input-label for="religion" value="Agama" />
                            <x-text-input id="religion" name="religion" type="text" class="block mt-1 w-full" value="{{ old('religion', $student->religion) }}" required />
                        </div>
                        <div>
                            <x-input-label for="phone" value="No. Handphone / WA" />
                            <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" value="{{ old('phone', $student->phone) }}" required />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="address" value="Alamat Lengkap" />
                            <textarea id="address" name="address" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>{{ old('address', $student->address) }}</textarea>
                        </div>
                    </div>

                    <!-- DATA ORANG TUA / WALI -->
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">B. Data Orang Tua / Wali</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <x-input-label for="parent_name" value="Nama Orang Tua / Wali" />
                            <x-text-input id="parent_name" name="parent_name" type="text" class="block mt-1 w-full" value="{{ old('parent_name', $student->parent_name) }}" required />
                        </div>
                        <div>
                            <x-input-label for="parent_phone" value="No. Handphone / WA Orang Tua" />
                            <x-text-input id="parent_phone" name="parent_phone" type="text" class="block mt-1 w-full" value="{{ old('parent_phone', $student->parent_phone) }}" required />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="parent_address" value="Alamat Lengkap Orang Tua" />
                            <textarea id="parent_address" name="parent_address" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>{{ old('parent_address', $student->parent_address) }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <x-primary-button class="px-6 py-3">Simpan Biodata</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>