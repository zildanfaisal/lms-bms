@extends('layouts.master')

@section('title', 'Preview PDF Report')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Preview PDF: Periode {{ $period->name ?? ('#'.$periodId) }}</h1>
@endsection

@section('content')
  <div class="space-y-6">
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <div class="text-sm text-gray-600">Pastikan data sudah sesuai sebelum unduh.</div>
        <a href="{{ route('learning.reports.export.pdf', ['period_id' => $periodId]) }}" class="px-4 py-2 rounded bg-indigo-600 text-white text-sm">Download PDF</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-xs">
          <thead>
            <tr class="border-b bg-gray-50">
              <th class="p-2 text-left">No</th>
              <th class="p-2 text-left">Employee</th>
              <th class="p-2 text-left">Direktorat</th>
              <th class="p-2 text-left">Divisi</th>
              <th class="p-2 text-left">Unit</th>
              <th class="p-2 text-left">Jabatan</th>
              <th class="p-2 text-right">Minutes</th>
              <th class="p-2 text-right">Target</th>
              <th class="p-2 text-right">Completion</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $i => $row)
              @php $t = $targetsMap[$row->karyawan_id] ?? null; @endphp
              <tr class="border-b">
                <td class="p-2">{{ $i+1 }}</td>
                <td class="p-2">{{ $row->owner->nama ?? ('#'.$row->karyawan_id) }}</td>
                <td class="p-2">{{ $row->owner->direktorat->nama_direktorat ?? '-' }}</td>
                <td class="p-2">{{ $row->owner->divisi->nama_divisi ?? '-' }}</td>
                <td class="p-2">{{ $row->owner->unit->nama_unit ?? '-' }}</td>
                <td class="p-2">{{ $row->owner->jabatan->nama_jabatan ?? '-' }}</td>
                <td class="p-2 text-right">{{ $row->minutes }}</td>
                <td class="p-2 text-right">{{ $t ?? '-' }}</td>
                <td class="p-2 text-right">@if($t) {{ number_format(min(100, ($row->minutes / max(1,$t)) * 100), 0) }}% @else - @endif</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
