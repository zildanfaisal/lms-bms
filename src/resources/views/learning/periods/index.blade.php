@extends('layouts.master')

@section('title','Learning Periods')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Learning Periods</h1>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow p-4">
    <div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="font-semibold">Daftar Periode</div>
        <a href="{{ route('learning.periods.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Tambah Periode</a>
    </div>

    @if(session('status'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form method="get" class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
        <div class="md:col-span-2">
        <input type="text" name="code" value="{{ request('code') }}" placeholder="Kode" class="border rounded w-full px-3 py-2 text-sm" />
        </div>
        <div class="md:col-span-2">
        <input type="text" name="name" value="{{ request('name') }}" placeholder="Nama" class="border rounded w-full px-3 py-2 text-sm" />
        </div>
        <div class="md:col-span-2 flex gap-2 items-center">
        <button class="inline-flex items-center px-3 py-2 rounded bg-gray-700 text-white text-sm">Filter</button>
        <a href="{{ route('learning.periods.index') }}" class="text-xs text-gray-600">Reset</a>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full text-sm divide-y">
        <thead>
            <tr class="bg-gray-50 text-left">
            <th class="px-4 py-2 text-xs text-gray-500">No</th>
            <th class="px-4 py-2 text-xs text-gray-500">Kode</th>
            <th class="px-4 py-2 text-xs text-gray-500">Nama</th>
              <th class="px-4 py-2 text-xs text-gray-500">Tanggal</th>
              <th class="px-4 py-2 text-xs text-gray-500">Locked</th>
              <th class="px-4 py-2 text-xs text-gray-500">Aktif</th>
            <th class="px-4 py-2 text-xs text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y">
            @forelse($periods as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($periods->currentPage() - 1) * $periods->perPage() }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ $p->code }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ $p->name }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ $p->starts_at?->toDateString() }} → {{ $p->ends_at?->toDateString() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if($p->is_locked)
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-red-100 text-red-600 text-xs">Yes</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-600 text-xs">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if($p->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-blue-100 text-blue-600 text-xs">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-200 text-gray-600 text-xs">Inactive</span>
                                @endif
                            </td>
                <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                <a href="{{ route('learning.periods.edit',$p) }}" class="text-purple-600 text-xs">Edit</a>
                                <form action="{{ route('learning.periods.destroy',$p) }}" method="post" data-confirm="Hapus periode ini?">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 text-xs" @if($p->is_locked) disabled @endif>Hapus</button>
                </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada data</td></tr>
            @endforelse
        </tbody>
        </table>
        <div class="p-4">{{ $periods->links() }}</div>
    </div>
    </div>
</div>
@endsection
