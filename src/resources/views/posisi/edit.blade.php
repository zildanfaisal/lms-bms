@extends('layouts.master')

@section('title', 'Edit Posisi')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Edit Posisi</h1>
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

        <form action="{{ route('posisi.update', $posisi->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700">Nama Posisi</label>
                    <input type="text" name="nama_posisi" value="{{ old('nama_posisi', $posisi->nama_posisi) }}" class="mt-1 block w-full rounded border-gray-200" required>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('posisi.index') }}" class="inline-flex items-center px-3 py-2 rounded bg-gray-200 text-sm">Batal</a>
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Simpan</button>
                </div>
            </div>
        </form>
    </div>
@endsection
