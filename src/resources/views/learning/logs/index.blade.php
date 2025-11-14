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
    </div>
    <div class="bg-white shadow-sm sm:rounded-lg p-6 mt-4">
      {{-- Rekomendasi Pembelajaran untuk Anda (periode terpilih) --}}
      <div class="mt-2">
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-sm font-semibold text-gray-800">Rekomendasi untuk Anda</h2>
          <a href="{{ route('dashboard', ['period_id' => $selectedPeriodId]) }}" class="text-xs text-indigo-600 hover:underline">Lihat di Dashboard</a>
        </div>
        @if (!empty($recommendedItems))
          <ul class="space-y-3">
            @foreach($recommendedItems as $idx => $item)
              @php($platformName = !empty($item['platform_id']) ? ($platformMap[$item['platform_id']] ?? null) : null)
              @php($isDone = !empty($item['done']) || !empty($item['pending']))
              <li class="p-3 rounded border flex items-start gap-3 recommended-item" data-done="{{ !empty($item['done']) ? '1' : '0' }}">
                <div class="mt-1 w-2 h-2 rounded-full bg-indigo-500"></div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <div class="text-sm font-medium text-gray-800 truncate">{{ $item['title'] ?? '-' }}</div>
                    @if($platformName)
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $platformName }}</span>
                    @endif
                    @if(!empty($item['done']))
                      <span class="inline-flex items-center text-green-600 text-[10px] gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8.5 12.086l6.793-6.793a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Selesai
                      </span>
                    @elseif(!empty($item['pending']))
                      <span class="inline-flex items-center text-yellow-600 text-[10px] gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-4.25a.75.75 0 001.5 0v-4.5a.75.75 0 00-1.5 0v4.5zM10 6a.875.875 0 110 1.75A.875.875 0 0110 6z" clip-rule="evenodd"/></svg>
                        Menunggu Approve
                      </span>
                    @endif
                  </div>
                  <div class="mt-2 flex items-center gap-2 flex-wrap text-xs text-gray-600">
                    @if(!empty($item['url']))
                      <a href="{{ $item['url'] }}" target="_blank" class="text-indigo-600 hover:underline">Buka Materi</a>
                    @endif
                    @if(!empty($item['target_minutes']))
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-gray-100 text-gray-700 border">Target {{ $item['target_minutes'] }}m</span>
                    @endif
                  </div>
                </div>
                <div class="pt-1">
                  <a href="{{ !$isDone ? route('learning.logs.create', [
                      'title' => $item['title'] ?? null,
                      'platform_id' => $item['platform_id'] ?? null,
                      'learning_url' => $item['url'] ?? null,
                      'duration_minutes' => $item['target_minutes'] ?? null,
                      'recommendation_id' => $item['id'] ?? null,
                      'period_id' => $selectedPeriodId ?? null,
                  ]) : '#' }}"
                     class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 {{ $isDone ? 'pointer-events-none opacity-50' : '' }}"
                     @if($isDone) aria-disabled="true" tabindex="-1" @endif
                  >Catat</a>
                </div>
              </li>
              @if ($idx >= 9)
                @break
              @endif
            @endforeach
          </ul>
        @else
          <div class="text-sm text-gray-500">Belum ada rekomendasi belajar untuk periode ini.</div>
        @endif
      </div>
      <div class="mt-4">{{ $logs->links() }}</div>
    </div>
@endsection
