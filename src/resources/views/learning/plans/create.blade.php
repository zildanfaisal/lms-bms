@extends('layouts.master')

@section('title', 'Buat Usulan Rencana Belajar')

@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Buat Usulan Rencana Belajar</h1>
@endsection

@section('content')
  <div class="bg-white rounded-xl shadow p-4">
    @if (session('status'))
      <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
      <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif

    <form action="{{ route('learning.plans.store') }}" method="POST">
      @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Periode</label>
                <select name="period_id" class="mt-1 w-full rounded tom-select" required>
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('period_id')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Scope</label>
                <div class="mt-1 grid grid-cols-1 gap-2">
                    <div class="flex gap-2 items-center">
                        <select id="scope-type" name="scope_type" class="rounded tom-select">
                            @foreach(($allowedScopeTypes ?? ['unit']) as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="scope-id" name="scope_id" required />
                    </div>
                    <!-- Cascading selects for Super Admin -->
                    <div id="cascade-direktorat" class="flex gap-2 items-center hidden">
                        <label class="text-xs text-gray-500 w-24">Direktorat</label>
                        <select id="sel-direktorat" class="flex-1 rounded tom-select">
                            <option value="">-- Pilih Direktorat --</option>
                            @foreach(($direktorats ?? []) as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_direktorat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="cascade-divisi" class="flex gap-2 items-center hidden">
                        <label class="text-xs text-gray-500 w-24">Divisi</label>
                        <select id="sel-divisi" class="flex-1 rounded tom-select">
                            <option value="">-- Pilih Divisi --</option>
                        </select>
                    </div>
                    <div id="cascade-unit" class="flex gap-2 items-center hidden">
                        <label class="text-xs text-gray-500 w-24">Unit</label>
                        <select id="sel-unit" class="flex-1 rounded tom-select">
                            <option value="">-- Pilih Unit --</option>
                            @if(($allowedScopeTypes ?? []) === ['unit'])
                            @foreach(($units ?? []) as $u)
                                <option value="{{ $u->id }}">{{ $u->nama_unit }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div id="impact-box" class="hidden mt-2 text-sm bg-gray-50 border rounded p-2">
                        <div class="font-medium">Dampak saat ini</div>
                        <div id="impact-summary" class="text-gray-700">-</div>
                    </div>
                </div>
                @error('scope_id')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Target Menit</label>
                <input type="number" min="1" step="1" name="target_minutes" class="mt-1 w-full border rounded p-2" />
                @error('target_minutes')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Catatan</label>
                <div class="mt-1 flex items-center gap-2">
                    <input type="text" name="notes" class="mt-1 w-full border rounded p-2" />
                </div>
            </div>
            <div>
                <input type="hidden" name="only_subordinate_jabatans" value="0" />
                <input id="only-subordinate" type="checkbox" name="only_subordinate_jabatans" value="1" class="rounded" />
                <label for="only-subordinate" class="text-sm text-gray-700">Berlaku hanya untuk jabatan di bawah Anda (khusus jika Scope = Unit)</label>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Rekomendasi</label>
            <div id="rec-list" class="mt-2 space-y-2"></div>
                <button type="button" id="add-rec" class="mt-2 px-3 py-1 rounded bg-gray-100">+ Tambah Rekomendasi</button>
                @error('recs')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mt-6">
            <button class="px-4 py-2 bg-purple-600 text-white rounded">Submit</button>
        </div>
    </form>
  </div>

  @push('scripts')
  <script>
    (function(){
      const addBtn = document.getElementById('add-rec');
      const list = document.getElementById('rec-list');
      function addRow(title='', url=''){
        const i = list.children.length;
        const row = document.createElement('div');
        row.className = 'grid grid-cols-1 md:grid-cols-2 gap-2';
        row.innerHTML = `
          <input name="recs[${i}][title]" placeholder="Judul" value="${title}" class="border rounded p-2" required />
          <input name="recs[${i}][url]" placeholder="URL (opsional)" value="${url}" class="border rounded p-2" />
        `;
        list.appendChild(row);
      }
      addBtn?.addEventListener('click', ()=> addRow());
      if(list.children.length === 0){ addRow(); }

      // Cascading selects and impact preview
      const scopeType = document.getElementById('scope-type');
      const scopeId = document.getElementById('scope-id');
      const cDir = document.getElementById('cascade-direktorat');
      const cDiv = document.getElementById('cascade-divisi');
      const cUnit = document.getElementById('cascade-unit');
      const selDir = document.getElementById('sel-direktorat');
      const selDiv = document.getElementById('sel-divisi');
      const selUnit = document.getElementById('sel-unit');
      const onlySub = document.getElementById('only-subordinate');
      const impactBox = document.getElementById('impact-box');
      const impactSummary = document.getElementById('impact-summary');

      function toggleVisible(el, show){ el.classList.toggle('hidden', !show); }
      function setScopeId(val){ scopeId.value = val || ''; fetchImpact(); }

      async function fetchDivisi(direktoratId){
        selDiv.innerHTML = '<option value="">-- Pilih Divisi --</option>';
        selUnit.innerHTML = '<option value="">-- Pilih Unit --</option>';
        if(!direktoratId) return;
        try{
          const res = await fetch(`{{ route('ajax.divisi.by.direktorat', ['direktorat' => '___ID___']) }}`.replace('___ID___', direktoratId));
          const data = await res.json();
          data.forEach(d=>{
            const o = document.createElement('option'); o.value = d.id; o.textContent = d.nama_divisi; selDiv.appendChild(o);
          });
        }catch(e){ console.error(e); }
      }
      async function fetchUnits(divisiId){
        selUnit.innerHTML = '<option value="">-- Pilih Unit --</option>';
        if(!divisiId) return;
        try{
          const res = await fetch(`{{ route('ajax.unit.by.divisi', ['divisi' => '___ID___']) }}`.replace('___ID___', divisiId));
          const data = await res.json();
          data.forEach(u=>{
            const o = document.createElement('option'); o.value = u.id; o.textContent = u.nama_unit; selUnit.appendChild(o);
          });
        }catch(e){ console.error(e); }
      }

      function onScopeTypeChange(){
        const type = scopeType.value;
        if(type === 'direktorat'){
          toggleVisible(cDir, true); toggleVisible(cDiv, false); toggleVisible(cUnit, false);
          setScopeId(selDir.value || '');
        } else if(type === 'divisi'){
          toggleVisible(cDir, true); toggleVisible(cDiv, true); toggleVisible(cUnit, false);
          setScopeId(selDiv.value || '');
        } else if(type === 'unit'){
          toggleVisible(cDir, true); toggleVisible(cDiv, true); toggleVisible(cUnit, true);
          // For manager (only units shown), scopeId derived from selUnit which might be prefilled
          setScopeId(selUnit.value || '');
        }
      }

      async function fetchImpact(){
        const type = scopeType.value;
        const id = scopeId.value;
        if(!type || !id) { impactBox.classList.add('hidden'); return; }
        try{
          const url = new URL(`{{ route('learning.plans.impact') }}`);
          url.searchParams.set('scope_type', type);
          url.searchParams.set('scope_id', id);
          url.searchParams.set('only_subordinate_jabatans', onlySub?.checked ? 1 : 0);
          const res = await fetch(url.toString());
          if(!res.ok){ impactBox.classList.add('hidden'); return; }
          const data = await res.json();
          impactSummary.textContent = `${data.total_jabatans} jabatan, ${data.total_karyawans} karyawan`;
          impactBox.classList.remove('hidden');
        }catch(e){ console.error(e); impactBox.classList.add('hidden'); }
      }

      // Wire events
      scopeType?.addEventListener('change', onScopeTypeChange);
      selDir?.addEventListener('change', (e)=>{ const id=e.target.value; if(!id){setScopeId(''); return;} fetchDivisi(id).then(()=>{ onScopeTypeChange(); }); setScopeId(scopeType.value==='direktorat'? id : ''); });
      selDiv?.addEventListener('change', (e)=>{ const id=e.target.value; if(!id){setScopeId(''); return;} fetchUnits(id).then(()=>{ onScopeTypeChange(); }); setScopeId(scopeType.value==='divisi'? id : ''); });
      selUnit?.addEventListener('change', (e)=> setScopeId(e.target.value));
      onlySub?.addEventListener('change', fetchImpact);

      // Initialize defaults: if only unit allowed (manager), show unit dropdown only
      if (scopeType && scopeType.options.length === 1 && scopeType.value === 'unit') {
        toggleVisible(cDir, false); toggleVisible(cDiv, false); toggleVisible(cUnit, true);
        setScopeId(selUnit.value || '');
      } else {
        // Super Admin default to direktorat scope; still require selection
        onScopeTypeChange();
      }
    })();
  </script>
  @endpush
@endsection
