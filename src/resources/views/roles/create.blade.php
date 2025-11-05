@extends('layouts.master')

@section('title', 'Tambah Role')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Tambah Role</h1>
@endsection

@section('content')
    <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="bg-white rounded-xl shadow p-4">
            <div class="mb-4">
                <label class="block text-sm text-gray-600">Nama Role</label>
                <input type="text" name="name" class="mt-1 w-full rounded border-gray-200" required>
            </div>
            <div>
                <div class="font-semibold mb-2">Permissions</div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-96 overflow-auto p-2 border rounded">
                    @foreach($permissions as $p)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $p->name }}"> {{ $p->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('roles.index') }}" class="px-3 py-2 rounded bg-gray-100">Batal</a>
                <button type="submit" class="px-3 py-2 rounded bg-purple-600 text-white">Simpan</button>
            </div>
        </div>
    </form>
@endsection
