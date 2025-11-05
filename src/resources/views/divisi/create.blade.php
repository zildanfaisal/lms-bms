@extends('layouts.master')

@section('title', 'Tambah Divisi')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Tambah Divisi</h1>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="list-disc ps-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('divisi.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700">Direktorat</label>
                    <select name="direktorat_id" class="mt-1 block w-full rounded border-gray-200 tom-select" required>
                        <option value="">-- Pilih Direktorat --</option>
                        @php $selectedDirektorat = old('direktorat_id', request('direktorat_id')); @endphp
                        @foreach($direktorats as $dir)
                            <option value="{{ $dir->id }}" {{ $selectedDirektorat == $dir->id ? 'selected' : '' }}>{{ $dir->nama_direktorat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Nama Divisi</label>
                    <input type="text" name="nama_divisi" value="{{ old('nama_divisi') }}" class="mt-1 block w-full rounded border-gray-200" required>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('divisi.index') }}" class="inline-flex items-center px-3 py-2 rounded bg-gray-200 text-sm">Batal</a>
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Simpan</button>
                </div>
            </div>
        </form>
    </div>
@endsection
