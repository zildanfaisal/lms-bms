@extends('layouts.master')

@section('title','Tambah Learning Period')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Tambah Learning Period</h1>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow p-4">
    <div class="max-w-xl">
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif
    <form action="{{ route('learning.periods.store') }}" method="post" class="space-y-4">
        @csrf
        <div>
        <label class="block text-sm font-medium mb-1">Kode</label>
        <input name="code" value="{{ old('code') }}" class="border rounded w-full px-3 py-2" />
        @error('code')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
        <label class="block text-sm font-medium mb-1">Nama</label>
        <input name="name" value="{{ old('name') }}" class="border rounded w-full px-3 py-2" />
        @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Mulai</label>
        <input type="date" name="starts_at" value="{{ old('starts_at') }}" class="border rounded w-full px-3 py-2" />
            @error('starts_at')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Selesai</label>
        <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="border rounded w-full px-3 py-2" />
            @error('ends_at')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
        </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} />
                <label class="text-sm">Aktifkan periode ini</label>
            </div>
        <div class="flex items-center gap-2">
        <input type="checkbox" name="is_locked" value="1" {{ old('is_locked') ? 'checked' : '' }} />
        <label class="text-sm">Kunci periode ini (tidak bisa diubah / dihapus)</label>
        </div>
        <div class="flex gap-2">
        <button class="inline-flex items-center px-4 py-2 rounded bg-purple-600 text-white text-sm">Simpan</button>
        <a href="{{ route('learning.periods.index') }}" class="inline-flex items-center px-4 py-2 rounded border text-sm">Batal</a>
        </div>
    </form>
    </div>
</div>
@endsection
