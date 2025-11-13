@extends('layouts.master')

@section('title', 'Edit Unit')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Edit Unit</h1>
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

    <form action="{{ route('unit.update', $unit->id) }}" method="POST" data-update-confirm="Simpan perubahan unit?">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700">Divisi</label>
                    <select name="divisi_id" class="mt-1 block w-full rounded border-gray-200 tom-select" required>
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisis as $div)
                            <option value="{{ $div->id }}" @if(old('divisi_id', $unit->divisi_id) == $div->id) selected @endif>{{ $div->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Nama Unit</label>
                    <input type="text" name="nama_unit" value="{{ old('nama_unit', $unit->nama_unit) }}" class="mt-1 block w-full rounded border-gray-200" required>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('unit.index') }}" class="inline-flex items-center px-3 py-2 rounded bg-gray-200 text-sm">Batal</a>
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Perbarui</button>
                </div>
            </div>
        </form>
    </div>
@endsection
