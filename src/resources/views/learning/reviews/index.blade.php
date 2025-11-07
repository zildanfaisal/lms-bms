@extends('layouts.master')

@section('title', 'Review Usulan Rencana')

@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Review Usulan Rencana</h1>
@endsection

@section('content')
  <div class="bg-white rounded-xl shadow p-4">
    <div class="font-semibold mb-3 flex items-center justify-between">
      <span>Antrean Usulan (Submitted)</span>
      <span class="text-xs text-gray-500">Total: {{ $proposals->total() }}</span>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-2">No</th>
            <th class="text-left p-2">Periode</th>
            <th class="text-left p-2">Pengusul</th>
            <th class="text-left p-2">Scope</th>
            <th class="text-left p-2">Target</th>
            <th class="text-left p-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($proposals as $i => $p)
            <tr class="border-b">
              <td class="p-2">{{ $proposals->firstItem() + $i }}</td>
              <td class="p-2">{{ $p->period->name ?? '-' }}</td>
              <td class="p-2">
                <div class="flex flex-col">
                  <span class="font-medium">{{ $p->proposer->name ?? '-' }}</span>
                  <span class="text-xs text-gray-500">{{ $p->proposer->karyawan->jabatan->nama_jabatan ?? 'Jabatan ?' }}</span>
                </div>
              </td>
              <td class="p-2">
                <div class="flex flex-col">
                  <span class="font-medium">{{ ucfirst($p->scope_type) }}</span>
                  <span class="text-xs text-gray-600">{{ $scopeNames[$p->scope_type.'-'.$p->scope_id] ?? '#'.$p->scope_id }}</span>
                  @if($p->scope_type==='unit' && $p->only_subordinate_jabatans)
                    <span class="mt-1 inline-block px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 text-xxs">Bawahan saja</span>
                  @endif
                </div>
              </td>
              <td class="p-2">
                <div class="flex flex-col text-xs">
                  <span class="font-medium">{{ $p->target_minutes ?? '-' }} menit</span>
                  <span class="text-gray-500">Jabatan: {{ $impact[$p->id]['jabatan'] }} | Karyawan: {{ $impact[$p->id]['karyawan'] }}</span>
                </div>
              </td>
              <td class="p-2">
                <a href="{{ route('learning.reviews.show',$p) }}" class="text-purple-600">Lihat</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $proposals->links() }}</div>
  </div>
@endsection
