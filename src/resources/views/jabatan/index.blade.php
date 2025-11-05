@extends('layouts.master')

@section('title', 'Jabatan')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Jabatan</h1>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">Jabatan</div>
                    <a href="{{ route('jabatan.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Tambah Jabatan</a>
                </div>
            </div>
            <div class="overflow-auto">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">No</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Kode</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Nama</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Level</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach($jabatans as $d)
                        <tr class="hover:bg-gray-50" data-href="{{ route('jabatan.edit', $d->id) }}">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($jabatans->currentPage() - 1) * $jabatans->perPage() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $d->kode_jabatan }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <a href="{{ route('jabatan.edit', $d->id) }}" class="text-purple-600" onclick="event.stopPropagation();">{{ $d->nama_jabatan }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $d->level }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                <x-action-button type="edit" href="{{ route('jabatan.edit', $d->id) }}" color="purple" />
                                <x-action-button type="delete" action="{{ route('jabatan.destroy', $d->id) }}" color="red" confirm="Hapus jabatan ini?" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
                <div class="mt-4">
                {{ $jabatans->links() }}
            </div>
        </div>
    </div>
@endsection
