@extends('layouts.master')

@section('title', 'Edit Divisi')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Edit Divisi</h1>
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

    <form action="{{ route('divisi.update', $divisi->id) }}" method="POST" data-update-confirm="Simpan perubahan divisi?">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700">Direktorat</label>
                    <select name="direktorat_id" class="mt-1 block w-full rounded border-gray-200 tom-select" required>
                        <option value="">-- Pilih Direktorat --</option>
                        @foreach($direktorats as $dir)
                            <option value="{{ $dir->id }}" @if(old('direktorat_id', $divisi->direktorat_id) == $dir->id) selected @endif>{{ $dir->nama_direktorat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Nama Divisi</label>
                    <input type="text" name="nama_divisi" value="{{ old('nama_divisi', $divisi->nama_divisi) }}" class="mt-1 block w-full rounded border-gray-200" required>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('divisi.index') }}" class="inline-flex items-center px-3 py-2 rounded bg-gray-200 text-sm">Batal</a>
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Perbarui</button>
                </div>
            </div>
        </form>
    </div>
@endsection
