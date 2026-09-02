<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Jurusan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('admin.majors.update', $major->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <x-input-label for="code" value="Kode / Singkatan" />
                        <x-text-input id="code" class="block mt-1 w-full bg-gray-50" type="text" name="code" value="{{ $major->code }}" required autofocus />
                    </div>
                    
                    <div class="mb-6">
                        <x-input-label for="name" value="Nama Program Keahlian" />
                        <x-text-input id="name" class="block mt-1 w-full bg-gray-50" type="text" name="name" value="{{ $major->name }}" required />
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.majors.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                        <x-primary-button>Update Jurusan</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>