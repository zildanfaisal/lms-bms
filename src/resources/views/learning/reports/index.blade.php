@extends('layouts.master')

@section('title', 'Learning Report')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Learning Report</h1>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
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
