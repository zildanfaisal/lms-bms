@extends('layouts.master')

@section('title', 'Tambah Permission')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Tambah Permission</h1>
@endsection

@section('content')
    <form action="{{ route('permissions.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="bg-white rounded-xl shadow p-4">
            <div class="mb-4">
                <label class="block text-sm text-gray-600">Nama Permission</label>
                <input type="text" name="name" class="mt-1 w-full rounded border-gray-200" placeholder="contoh: export laporan" required>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('permissions.index') }}" class="px-3 py-2 rounded bg-gray-100">Batal</a>
                <button type="submit" class="px-3 py-2 rounded bg-purple-600 text-white">Simpan</button>
            </div>
        </div>
    </form>
@endsection
