@extends('layouts.master')

@section('title', 'My Learning Logs')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">My Learning Logs</h1>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
      @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
      @endif

      <form id="periodFilterForm" method="GET" action="{{ route('learning.logs.index') }}" class="mb-4">
        <div class="flex flex-wrap gap-3 items-end">
          <div>
            <label class="block text-sm font-medium">Filter Periode</label>
            <select name="period_id" class="mt-1 mb-4 tom-select" onchange="document.getElementById('periodFilterForm').submit()">
              <option value="">-- Pilih Periode --</option>
              @foreach($periodOptions as $p)
                <option value="{{ $p->id }}" @selected(request('period_id') == $p->id)>{{ $p->name }}</option>
              @endforeach
            </select>
            <div class="flex gap-2 md:justify-start mb-4">
              <a href="{{ route('learning.logs.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">+ Tambah Data</a>
            </div>
          </div>
        </div>
      </form>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">No</th>
              <th class="text-left p-2">Platform</th>
              <th class="text-left p-2">Title</th>
              <th class="text-left p-2">Duration</th>
              <th class="text-left p-2">Status</th>
              <th class="text-left p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($logs as $i => $log)
            <tr class="border-b">
              <td class="p-2">{{ $logs->firstItem() + $i }}</td>
              <td class="p-2">{{ $log->platform->name ?? '-' }}</td>
              <td class="p-2">{{ $log->title }}</td>
              <td class="p-2">{{ $log->duration_minutes }} mins</td>
              <td class="p-2 capitalize">{{ $log->status }}</td>
              <td class="p-2 space-x-2">
                <a href="{{ route('learning.logs.show',$log) }}" class="text-blue-600">View</a>
                @if($log->status === 'draft')
                  @can('submit learning log')
                  <form method="POST" action="{{ route('learning.logs.submit',$log) }}" class="inline" data-confirm="Kirim log untuk persetujuan?">
                    @csrf
                    <button class="text-green-700">Submit</button>
                  </form>
                  @endcan
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $logs->links() }}</div>
    </div>
@endsection
