<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Akun Kaprodi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 max-w-2xl mx-auto">
                <form action="{{ route('admin.kaprodi.update', $kaprodi->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <x-input-label for="name" value="Nama Lengkap & Gelar" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name', $kaprodi->name) }}" required autofocus />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="nip" value="NIP (Opsional)" />
                        <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" value="{{ old('nip', $kaprodi->nip) }}" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="major_id" value="Jurusan yang Dikepalai" />
                        <select id="major_id" name="major_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ $kaprodi->major_id == $major->id ? 'selected' : '' }}>{{ $major->code }} - {{ $major->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <x-input-label for="email" value="Email Login" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email', $kaprodi->user->email) }}" required />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="password" value="Password Baru (Kosongkan jika tidak ingin mengubah)" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                    </div>
                    
                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.kaprodi.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                        <x-primary-button>
                            {{ __('Update Akun') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
