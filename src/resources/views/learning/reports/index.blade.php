@extends('layouts.master')

@section('title', 'Learning Report')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Learning Report</h1>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
      <form method="GET" action="{{ route('learning.reports.index') }}" class="flex items-end gap-3 mb-4">
        <div>
          <label class="block text-sm text-gray-600">Periode</label>
          <select name="period_id" class="mt-1 rounded tom-select">
            <option value="">(Semua)</option>
            @foreach(($periodOptions ?? []) as $p)
              <option value="{{ $p->id }}" @selected($periodId == $p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <button class="px-3 py-2 rounded bg-indigo-600 text-white text-sm">Filter</button>
        </div>
        <div class="ml-auto">
          <a href="{{ route('learning.reports.export', ['period_id' => $periodId]) }}" class="px-3 py-2 rounded bg-green-600 text-white text-sm">Export CSV</a>
        </div>
      </form>
      @if(!empty($avgCompletion))
        <div class="mb-3 text-sm text-gray-600">Rata-rata penyelesaian (baris ditampilkan): <span class="font-medium text-green-700">{{ $avgCompletion }}%</span></div>
      @endif
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">#</th>
              <th class="text-left p-2">Employee</th>
              <th class="text-left p-2">Total Minutes</th>
              <th class="text-left p-2">Target Minutes</th>
              <th class="text-left p-2">Completion</th>
            </tr>
          </thead>
          <tbody>
            @foreach($summary as $i => $row)
            <tr class="border-b">
              <td class="p-2">{{ $summary->firstItem() + $i }}</td>
              <td class="p-2">{{ $row->owner->nama ?? '-' }}</td>
              <td class="p-2">{{ $row->minutes }}</td>
              @php $t = $targetsMap[$row->karyawan_id] ?? null; @endphp
              <td class="p-2">{{ $t ?? '-' }}</td>
              <td class="p-2">
                @if($t)
                  {{ number_format(min(100, ($row->minutes / max(1,$t)) * 100), 0) }}%
                @else
                  -
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $summary->links() }}</div>
    </div>
@endsection
