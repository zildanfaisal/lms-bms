@extends('layouts.master')

@section('title', 'Learning Report Detail')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Detail Report: {{ $karyawan->nama ?? ('#'.$karyawan->id) }}</h1>
@endsection

@section('content')
  <div class="space-y-6">
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
          <div><span class="text-gray-500">Direktorat:</span> {{ $karyawan->direktorat->nama_direktorat ?? '-' }}</div>
          <div><span class="text-gray-500">Divisi:</span> {{ $karyawan->divisi->nama_divisi ?? '-' }}</div>
          <div><span class="text-gray-500">Unit:</span> {{ $karyawan->unit->nama_unit ?? '-' }}</div>
        </div>
        <div>
          <div><span class="text-gray-500">Jabatan:</span> {{ $karyawan->jabatan->nama_jabatan ?? '-' }}</div>
          <div><span class="text-gray-500">Target (menit):</span> {{ $target ?? '-' }}</div>
          <div><span class="text-gray-500">Approved minutes:</span> {{ $minutes }}</div>
        </div>
      </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6">
      <div class="mb-3 font-medium flex items-center justify-between">
        <span>Log Pembelajaran (disetujui)</span>
        <form method="GET" class="flex items-center gap-2">
          <select name="period_id" class="border rounded p-1 text-xs">
            <option value="">(Aktif)</option>
            @foreach(\App\Models\LearningPeriod::orderByDesc('starts_at')->get(['id','name']) as $p)
              <option value="{{ $p->id }}" @selected(request('period_id') == $p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
          <button class="px-2 py-1 bg-indigo-600 text-white rounded text-xs">Ganti</button>
        </form>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">Tanggal</th>
              <th class="text-left p-2">Judul/Deskripsi</th>
              <th class="text-left p-2">Platform</th>
              <th class="text-right p-2">Menit</th>
              <th class="text-left p-2">Bukti</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
              <tr class="border-b">
                <td class="p-2">{{ optional($log->created_at)->format('Y-m-d') }}</td>
                <td class="p-2">{{ $log->title ?? '-' }}</td>
                <td class="p-2">{{ $log->platform->name ?? '-' }}</td>
                <td class="p-2 text-right">{{ $log->duration_minutes }}</td>
                <td class="p-2">@if(!empty($log->evidence_url)) <a href="{{ $log->evidence_url }}" class="text-indigo-600 hover:underline" target="_blank">Buka</a> @else - @endif</td>
              </tr>
            @empty
              <tr><td class="p-3 text-gray-500" colspan="5">Tidak ada data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $logs->withQueryString()->links() }}</div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6">
      <div class="mb-3 font-medium">Rekomendasi yang Berlaku</div>
      @if(!empty($recs))
        <ul class="text-sm list-disc pl-5">
          @foreach($recs as $item)
            <li>
              <span class="font-medium">{{ $item['title'] }}</span>
              @if(!empty($item['url']))
                <a href="{{ $item['url'] }}" target="_blank" class="text-indigo-600 hover:underline ml-2">Buka</a>
              @endif
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-gray-500 text-sm">Tidak ada rekomendasi untuk periode ini.</div>
      @endif
    </div>
  </div>
@endsection
