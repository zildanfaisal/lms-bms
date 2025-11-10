@extends('layouts.master')

@section('title', 'Learning Report')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Learning Report</h1>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
    <form id="reportFilterForm" method="GET" action="{{ route('learning.reports.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 items-end">
        <div>
          <label class="block text-sm text-gray-600">Periode</label>
          <select name="period_id" class="mt-1 rounded tom-select w-full">
            <option value="">(Semua)</option>
            @foreach(($periodOptions ?? []) as $p)
              <option value="{{ $p->id }}" @selected($periodId == $p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
        @role('Super Admin')
        <div>
          <label class="block text-sm text-gray-600">Direktorat</label>
          <select name="direktorat_id" class="mt-1 rounded tom-select w-full">
            <option value="">(Semua)</option>
            @foreach(($direktoratOptions ?? []) as $d)
              <option value="{{ $d->id }}" @selected($direktoratId == $d->id)>{{ $d->nama_direktorat }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-600">Divisi</label>
          <select name="divisi_id" class="mt-1 rounded tom-select w-full">
            <option value="">(Semua)</option>
            @foreach(($divisiOptions ?? []) as $dv)
              <option value="{{ $dv->id }}" @selected($divisiId == $dv->id)>{{ $dv->nama_divisi }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-600">Unit</label>
          <select name="unit_id" class="mt-1 rounded tom-select w-full">
            <option value="">(Semua)</option>
            @foreach(($unitOptions ?? []) as $u)
              <option value="{{ $u->id }}" @selected($unitId == $u->id)>{{ $u->nama_unit }}</option>
            @endforeach
          </select>
        </div>
        @endrole
      </form>
      <div class="flex gap-2 md:justify-start mb-4">
        <a href="{{ route('learning.reports.export.pdf', ['period_id' => $periodId, 'direktorat_id' => $direktoratId, 'divisi_id' => $divisiId, 'unit_id' => $unitId]) }}" target="_blank" class="px-3 py-2 rounded bg-green-600 text-white text-sm">Export PDF</a>
      </div>
      @if(!empty($avgCompletion))
        <div class="mb-4 text-sm text-gray-600">Rata-rata penyelesaian (baris ditampilkan): <span class="font-medium text-green-700">{{ $avgCompletion }}%</span></div>
      @endif
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">No</th>
              <th class="text-left p-2">Employee</th>
              <th class="text-left p-2">Jabatan</th>
              <th class="text-left p-2">Minutes</th>
              <th class="text-left p-2">Target</th>
              <th class="text-left p-2">Completion</th>
            </tr>
          </thead>
          <tbody>
            @foreach($summary as $i => $row)
            <tr class="border-b">
              <td class="p-2">{{ $summary->firstItem() + $i }}</td>
              <td class="p-2">
                <a href="{{ route('learning.reports.show', ['karyawan' => $row->karyawan_id, 'period_id' => $periodId]) }}" class="text-indigo-600 hover:underline">
                  {{ $row->owner->nama ?? ('#'.$row->karyawan_id) }}
                </a>
              </td>
              <td class="p-2">{{ $row->owner->jabatan->nama_jabatan ?? '-' }}</td>
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

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('reportFilterForm');
    if (!form) return;

    const byName = (n) => form.querySelector('select[name="' + n + '"]');
    const periodSel = byName('period_id');
    const dirSel = byName('direktorat_id');
    const divSel = byName('divisi_id');
    const unitSel = byName('unit_id');

    const submit = () => {
      // ensure TomSelect pushes current values to the native select before submit
      [periodSel, dirSel, divSel, unitSel].forEach(function(el){ if (el && el.tomselect) el.tomselect.sync(true); });
      form.submit();
    };

    if (periodSel) periodSel.addEventListener('change', submit);
    if (dirSel) dirSel.addEventListener('change', function(){
      // reset lower levels
      if (divSel && divSel.tomselect) { divSel.tomselect.clear(true); }
      if (unitSel && unitSel.tomselect) { unitSel.tomselect.clear(true); }
      submit();
    });
    if (divSel) divSel.addEventListener('change', function(){
      if (unitSel && unitSel.tomselect) { unitSel.tomselect.clear(true); }
      submit();
    });
    if (unitSel) unitSel.addEventListener('change', submit);
  });
</script>
@endpush
