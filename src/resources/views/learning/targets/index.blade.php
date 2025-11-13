@extends('layouts.master')

@section('title', 'Learning Targets')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Learning Targets</h1>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg p-6">

      @if (session('status'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
          <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('learning.targets.store') }}" class="mb-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium">Period</label>
            <select name="period_id" class="mt-1 w-full border rounded p-2 tom-select" required>
              <option value="">-- Select Period --</option>
              @foreach(($periods ?? []) as $p)
                <option value="{{ $p->id }}" @selected(old('period_id', $periodId) == $p->id)>{{ $p->name }}</option>
              @endforeach
            </select>
            @error('period_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium">Direktorat</label>
            <select name="direktorat_id" id="target-direktorat" class="mt-1 w-full border rounded p-2 tom-select">
              <option value="">-- Semua Direktorat --</option>
              @foreach(($direktorats ?? []) as $d)
                <option value="{{ $d->id }}" @selected(old('direktorat_id') == $d->id)>{{ $d->nama_direktorat }}</option>
              @endforeach
            </select>
            @error('direktorat_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium">Divisi</label>
            <select name="divisi_id" id="target-divisi" class="mt-1 w-full border rounded p-2 tom-select">
              <option value="">-- Semua Divisi --</option>
              @foreach(($divisis ?? []) as $d)
                <option value="{{ $d->id }}" @selected(old('divisi_id') == $d->id)>{{ $d->nama_divisi }}</option>
              @endforeach
            </select>
            @error('divisi_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium">Target (minutes)</label>
            <input name="target_minutes" type="number" min="0" class="mt-1 w-full border rounded p-2" value="{{ old('target_minutes') }}" required />
            @error('target_minutes')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
          </div>

          <div class="md:col-span-6">
            <p class="text-xs text-gray-600 mb-2">Kosongkan Divisi untuk menetapkan target ke seluruh Direktorat. Biarkan Direktorat kosong jika ingin menetapkan target pada semua Divisi (tidak disarankan).</p>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Target</button>
          </div>
        </div>
      </form>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">#</th>
              <th class="text-left p-2">Period</th>
              <th class="text-left p-2">Scope</th>
              <th class="text-left p-2">Minutes</th>
              <th class="text-left p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($targets as $i => $t)
            <tr class="border-b">
              <td class="p-2">{{ $targets->firstItem() + $i }}</td>
              <td class="p-2">{{ $t->period->name }}</td>
              <td class="p-2">
                @if($t->karyawan) Karyawan: {{ $t->karyawan->nama }} @endif
                @if($t->jabatan) Jabatan: {{ $t->jabatan->nama_jabatan }} @endif
                @if($t->unit) Unit: {{ $t->unit->nama_unit }} @endif
                @if($t->divisi) Divisi: {{ $t->divisi->nama_divisi }} @endif
                @if($t->direktorat) Direktorat: {{ $t->direktorat->nama_direktorat }} @endif
              </td>
              <td class="p-2">
                <form method="POST" action="{{ route('learning.targets.update', $t) }}" class="flex items-center gap-2">
                  @csrf
                  @method('PUT')
                  <input name="target_minutes" type="number" min="0" value="{{ $t->target_minutes }}" class="w-24 border rounded p-1" />
                  <button class="px-2 py-1 bg-indigo-600 text-white rounded text-xs">Update</button>
                </form>
              </td>
              <td class="p-2">
                <form method="POST" action="{{ route('learning.targets.destroy', $t) }}" data-confirm="Hapus target ini?">
                  @csrf
                  @method('DELETE')
                  <button class="px-2 py-1 bg-red-600 text-white rounded text-xs">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $targets->links() }}</div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const selDir = document.getElementById('target-direktorat');
  const selDiv = document.getElementById('target-divisi');
  const divisiUrlTmpl = "{{ route('ajax.divisi.by.direktorat', ['direktorat' => '__ID__']) }}";

  function clearDivisi(){
    if (!selDiv) return;
    if (selDiv.tomselect) {
      selDiv.tomselect.clearOptions();
      selDiv.tomselect.addOption([{value: '', text: '-- Semua Divisi --'}]);
      selDiv.tomselect.setValue('');
    } else {
      selDiv.innerHTML = '<option value="">-- Semua Divisi --</option>';
    }
  }

  async function populateDivisis(dirId){
    clearDivisi();
    if (!dirId) return;
    try{
      const url = divisiUrlTmpl.replace('__ID__', encodeURIComponent(dirId));
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const list = await res.json();
      if (selDiv.tomselect) {
        list.forEach(d => selDiv.tomselect.addOption({value: d.id, text: d.nama_divisi}));
        selDiv.tomselect.refreshOptions(false);
      } else {
        list.forEach(d => {
          const opt = document.createElement('option');
          opt.value = d.id; opt.textContent = d.nama_divisi; selDiv.appendChild(opt);
        });
      }
    }catch(e){ console.error(e); }
  }

  if (selDir){
    selDir.addEventListener('change', (e)=>{
      populateDivisis(e.target.value);
    });
  }

  // If old('direktorat_id') available, pre-populate
  const initialDir = "{{ old('direktorat_id') }}";
  const initialDiv = "{{ old('divisi_id') }}";
  if (initialDir) {
    populateDivisis(initialDir).then(()=>{
      if (initialDiv) {
        if (selDiv.tomselect) selDiv.tomselect.setValue(initialDiv); else selDiv.value = initialDiv;
      }
    });
  }
});
</script>
@endpush
