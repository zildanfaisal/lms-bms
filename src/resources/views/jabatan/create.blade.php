@extends('layouts.master')

@section('title', 'Tambah Jabatan')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Tambah Jabatan</h1>
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

        <form action="{{ route('jabatan.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700">Kode Jabatan</label>
                    <input type="text" name="kode_jabatan" value="{{ old('kode_jabatan') }}" class="mt-1 block w-full rounded border-gray-200" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Nama Jabatan</label>
                    <input type="text" name="nama_jabatan" value="{{ old('nama_jabatan') }}" class="mt-1 block w-full rounded border-gray-200" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Level</label>
                    <input type="number" name="level" value="{{ old('level', 0) }}" class="mt-1 block w-full rounded border-gray-200" min="1">
                    <p class="text-xs text-gray-500 mt-1">Tingkat hirarki (semakin besar angkanya = semakin tinggi posisi)</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('jabatan.index') }}" class="inline-flex items-center px-3 py-2 rounded bg-gray-200 text-sm">Batal</a>
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Simpan</button>
                </div>
            </div>
        </form>
    </div>
@endsection
