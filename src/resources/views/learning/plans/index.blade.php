@extends('layouts.master')

@section('title', 'Usulan Rencana Belajar')

@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Usulan Rencana Belajar</h1>
@endsection

@section('content')
  <div class="bg-white rounded-xl shadow p-4">
    @if (session('status'))
      <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
      <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif
    
    <div class="flex flex-col gap-4 mb-4">
      <div class="flex justify-between items-center">
        <div class="font-semibold">Daftar Usulan</div>
        <a href="{{ route('learning.plans.create') }}" class="px-3 py-2 rounded bg-purple-600 text-white text-sm">Buat Usulan</a>
      </div>
      <form method="GET" class="bg-gray-50 rounded p-3 grid grid-cols-1 md:grid-cols-5 gap-3 text-sm">
        <div>
          <label class="block text-xs text-gray-600">Periode</label>
          <select name="period_id" class="mt-1 w-full border rounded p-1">
            <option value="">Semua</option>
            @foreach($periodOptions as $po)
              <option value="{{ $po->id }}" @selected($activeFilters['period_id']==$po->id)>{{ $po->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-600">Status</label>
          <select name="status" class="mt-1 w-full border rounded p-1">
            <option value="">Semua</option>
            @foreach(['draft','submitted','approved','rejected'] as $st)
              <option value="{{ $st }}" @selected($activeFilters['status']==$st)>{{ ucfirst($st) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-600">Scope Type</label>
          <select name="scope_type" class="mt-1 w-full border rounded p-1">
            <option value="">Semua</option>
            @foreach($allowedScopeTypes as $st)
              <option value="{{ $st }}" @selected($activeFilters['scope_type']==$st)>{{ ucfirst($st) }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex items-end gap-2 md:col-span-2">
          <button class="px-3 py-2 bg-indigo-600 text-white rounded">Filter</button>
          <a href="{{ route('learning.plans.index') }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded">Reset</a>
        </div>
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-2">No</th>
            <th class="text-left p-2">Periode</th>
            <th class="text-left p-2">Scope</th>
            <th class="text-left p-2">Target (menit)</th>
            <th class="text-left p-2">Status</th>
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
                  <span class="font-medium">{{ ucfirst($p->scope_type) }}</span>
                  <span class="text-xs text-gray-600">{{ $scopeNames[$p->scope_type.'-'.$p->scope_id] ?? '#'.$p->scope_id }}</span>
                </div>
              </td>
              <td class="p-2">{{ $p->target_minutes ?? '-' }}</td>
              <td class="p-2">
                @php
                  $statusColor = match($p->status) {
                    'draft' => 'bg-gray-200 text-gray-700',
                    'submitted' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-600'
                  };
                @endphp
                <span class="px-2 py-1 rounded text-xs font-medium {{ $statusColor }}">{{ ucfirst($p->status) }}</span>
                @if($p->scope_type==='unit' && $p->only_subordinate_jabatans)
                  <span class="ml-1 px-2 py-1 rounded text-xs bg-indigo-50 text-indigo-700">Bawahan</span>
                @endif
              </td>
              <td class="p-2 space-x-2">
                @if($p->status === 'draft')
                  <a href="{{ route('learning.plans.edit',$p) }}" class="text-purple-600">Edit</a>
                  <form action="{{ route('learning.plans.submit',$p) }}" method="POST" class="inline" onsubmit="return confirm('Kirim usulan ke HR?')">
                    @csrf
                    <button class="text-green-700">Submit</button>
                  </form>
                @else
                  <a href="{{ route('learning.plans.edit',$p) }}" class="text-gray-400 pointer-events-none">Edit</a>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $proposals->links() }}</div>
  </div>
@endsection
