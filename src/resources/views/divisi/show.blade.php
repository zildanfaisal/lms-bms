@extends('layouts.master')

@section('title', 'Detail Divisi')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Detail Divisi</h1>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Divisi</div>
                        <div class="text-lg font-semibold">{{ $divisi->nama_divisi }}</div>
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-gray-500">Jumlah Unit</div>
                        <div class="text-lg font-semibold">{{ $countUnits }}</div>
                        <div class="mt-2">
                            <a href="{{ route('unit.create', ['divisi_id' => $divisi->id]) }}" class="inline-flex items-center px-3 py-2 rounded bg-green-600 text-white text-sm">Tambah Unit</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <h3 class="text-sm font-semibold mb-2">Daftar Unit</h3>
                <div class="overflow-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">Nama Unit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @foreach($units as $unit)
                                <tr class="hover:bg-gray-50" data-href="{{ route('unit.edit', $unit->id) }}">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($units->currentPage() - 1) * $units->perPage() }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <a href="{{ route('unit.edit', $unit->id) }}" class="text-purple-600" onclick="event.stopPropagation();">{{ $unit->nama_unit }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $units->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
