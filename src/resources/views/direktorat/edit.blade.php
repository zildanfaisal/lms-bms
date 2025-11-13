@extends('layouts.master')

@section('title', 'Edit Direktorat')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Edit Direktorat</h1>
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

    <form action="{{ route('direktorat.update', $direktorat->id) }}" method="POST" data-update-confirm="Simpan perubahan direktorat?">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700">Nama Direktorat</label>
                    <input type="text" name="nama_direktorat" value="{{ old('nama_direktorat', $direktorat->nama_direktorat) }}" class="mt-1 block w-full rounded border-gray-200" required>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('direktorat.index') }}" class="inline-flex items-center px-3 py-2 rounded bg-gray-200 text-sm">Batal</a>
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Perbarui</button>
                </div>
            </div>
        </form>
    </div>
@endsection
