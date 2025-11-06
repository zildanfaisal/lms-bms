@extends('layouts.master')

@section('title', 'Karyawan')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Karyawan</h1>
@endsection

@section('content')
    <div id="page" data-can-edit="{{ auth()->user()->can('update karyawan') ? 1 : 0 }}" data-can-delete="{{ auth()->user()->can('delete karyawan') ? 1 : 0 }}" class="bg-white rounded-xl shadow overflow-hidden">
                <div class="p-4 border-b">
                    <div class="flex flex-col gap-3">
                        <div class="flex justify-between items-center">
                            <div class="font-semibold">Karyawan</div>
                            <div class="flex items-center gap-3">
                                @can('create karyawan')
                                    <a href="{{ route('karyawan.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-green-600 text-white text-sm">Tambah Karyawan</a>
                                @endcan
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                            <div>
                                <label class="text-xs text-gray-600">Cari Nama</label>
                                <input id="karyawan-search" type="text" name="q" value="{{ request('q', '') }}" placeholder="Cari NIK, nama, email, unit..." class="w-full rounded border-gray-200 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Direktorat</label>
                                <select id="filter-direktorat" class="w-full rounded border-gray-200 text-sm tom-select">
                                    <option value="">Semua Direktorat</option>
                                    @isset($direktorats)
                                        @foreach($direktorats as $d)
                                            <option value="{{ $d->id }}" @selected(($dirId ?? null) == $d->id)>{{ $d->nama_direktorat }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Divisi</label>
                                <select id="filter-divisi" class="w-full rounded border-gray-200 text-sm tom-select">
                                    <option value="">Semua Divisi</option>
                                    @isset($divisis)
                                        @foreach($divisis as $d)
                                            <option value="{{ $d->id }}" @selected(($divId ?? null) == $d->id)>{{ $d->nama_divisi }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600">Unit</label>
                                <select id="filter-unit" class="w-full rounded border-gray-200 text-sm tom-select">
                                    <option value="">Semua Unit</option>
                                    @isset($units)
                                        @foreach($units as $u)
                                            <option value="{{ $u->id }}" @selected(($unitId ?? null) == $u->id)>{{ $u->nama_unit }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="flex items-end md:justify-end">
                                <x-action-button type="reset" id="karyawan-reset" class="w-full md:inline-flex md:items-center md:px-3 md:py-2 px-2 py-1 rounded text-sm text-red-600">Reset</x-action-button>
                            </div>
                        </div>
                    </div>
                </div>
        <div class="overflow-auto">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">No</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Nama</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Direktorat</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Divisi</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Unit</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Jabatan</th>
                        @can('update karyawan')
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @foreach($karyawans as $k)
                        @can('update karyawan')
                        <tr class="hover:bg-gray-50" data-href="{{ route('karyawan.edit', $k->id) }}">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($karyawans->currentPage() - 1) * $karyawans->perPage() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->nama }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->direktorat?->nama_direktorat ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->divisi?->nama_divisi ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->unit?->nama_unit ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->jabatan?->nama_jabatan ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                @can('update karyawan')
                                    <x-action-button type="edit" href="{{ route('karyawan.edit', $k->id) }}" color="purple">Edit</x-action-button>
                                @endcan
                                @can('delete karyawan')
                                    <x-action-button type="delete" action="{{ route('karyawan.destroy', $k->id) }}" color="red" confirm="Hapus karyawan ini?" />
                                @endcan
                            </td>
                        </tr>
                        @else
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($karyawans->currentPage() - 1) * $karyawans->perPage() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->nama }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->direktorat?->nama_direktorat ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->divisi?->nama_divisi ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->unit?->nama_unit ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $k->jabatan?->nama_jabatan ?? '-' }}</td>
                        </tr>
                        @endcan
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $karyawans->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    @include('components.instant-search-scripts')
    <script>
    (function(){
        const page = document.getElementById('page');
        const CAN_EDIT = page?.dataset?.canEdit === '1';
        const CAN_DELETE = page?.dataset?.canDelete === '1';
        const input = document.getElementById('karyawan-search');
        const resetBtn = document.getElementById('karyawan-reset');
        const tbody = document.querySelector('table tbody');
        const pagination = document.querySelector('.p-4');
        const selDir = document.getElementById('filter-direktorat');
        const selDiv = document.getElementById('filter-divisi');
        const selUnit = document.getElementById('filter-unit');

        const divisiUrlTmpl = "{{ route('ajax.divisi.by.direktorat', ['direktorat' => '__ID__']) }}";
        const unitUrlTmpl = "{{ route('ajax.unit.by.divisi', ['divisi' => '__ID__']) }}";

        function buildParams(q){
            const params = new URLSearchParams();
            if(q) params.set('q', q);
            const dir = document.getElementById('filter-direktorat')?.value || '';
            const div = document.getElementById('filter-divisi')?.value || '';
            const unit = document.getElementById('filter-unit')?.value || '';
            if(dir) params.set('direktorat_id', dir);
            if(div) params.set('divisi_id', div);
            if(unit) params.set('unit_id', unit);
            return params.toString();
        }

        async function doSearch(q){
            if(!q){ resetBtn.classList.add('hidden'); location.search = ''; return; }
            resetBtn.classList.remove('hidden');
            try{
                const res = await fetch(`{{ route('karyawan.index') }}?${buildParams(q)}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                tbody.innerHTML = data.map((item, idx) => `\
                    <tr class="hover:bg-gray-50" data-href="/karyawan/${item.id}/edit">\
                        <td class="px-4 py-3 text-sm text-gray-700">${idx+1}</td>\
                        <td class="px-4 py-3 text-sm text-gray-700">${IS.escape(item.nama)}</td>\
                        <td class="px-4 py-3 text-sm text-gray-700">${IS.escape(item.direktorat || '-')}</td>\
                        <td class="px-4 py-3 text-sm text-gray-700">${IS.escape(item.divisi || '-')}</td>\
                        <td class="px-4 py-3 text-sm text-gray-700">${IS.escape(item.unit || '-')}</td>\
                        <td class="px-4 py-3 text-sm text-gray-700">${IS.escape(item.jabatan || '-')}</td>\
                        <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">\
                            ${CAN_EDIT ? IS.renderEdit(`/karyawan/${item.id}/edit`, 'text-purple-600') : ''}\
                            ${CAN_DELETE ? IS.renderDelete(`/karyawan/${item.id}`, 'Hapus karyawan ini?', 'text-red-600') : ''}\
                        </td>\
                    </tr>`).join('');
                if(pagination) pagination.style.display = 'none';
            }catch(e){ console.error(e); }
        }

        const deb = IS.debounce(e => doSearch(e.target.value), 300);
        if(input){ input.addEventListener('input', deb); }
        if(resetBtn){ resetBtn.addEventListener('click', ()=>{
            // clear inputs and filters
            input.value = '';
            if (selDir) {
                if (selDir.tomselect) selDir.tomselect.setValue(''); else selDir.value = '';
            }
            if (selDiv) {
                if (selDiv.tomselect) selDiv.tomselect.setValue(''); else selDiv.value = '';
            }
            if (selUnit) {
                if (selUnit.tomselect) selUnit.tomselect.setValue(''); else selUnit.value = '';
            }
            // navigate to base listing (clear query string)
            window.location.href = `{{ route('karyawan.index') }}`;
        }); }

        // On changing any filter, reload page with query params (server-rendered list)
        function applyFilters(){
            const s = buildParams(input?.value || '');
            const url = `{{ route('karyawan.index') }}${s ? ('?'+s) : ''}`;
            window.location.href = url;
        }

        let suppressFilterApply = false;

        async function populateDivisis(direktoratId, keepValue){
            if(!selDiv) return;
            // clear divisi and unit
            if (selDiv.tomselect) {
                selDiv.tomselect.clearOptions();
                selDiv.tomselect.addOption([{value: '', text: 'Semua Divisi'}]);
            } else {
                selDiv.innerHTML = '<option value="">Semua Divisi</option>';
            }
            if(selUnit) {
                if (selUnit.tomselect) {
                    selUnit.tomselect.clearOptions();
                    selUnit.tomselect.addOption([{value: '', text: 'Semua Unit'}]);
                } else {
                    selUnit.innerHTML = '<option value="">Semua Unit</option>';
                }
            }
            if(!direktoratId) return;
            try{
                // suppress change handlers while we programmatically update options/values
                suppressFilterApply = true;
                const url = divisiUrlTmpl.replace('__ID__', encodeURIComponent(direktoratId));
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const list = await res.json();
                if (selDiv.tomselect) {
                    list.forEach(d => selDiv.tomselect.addOption({value: d.id, text: d.nama_divisi}));
                    selDiv.tomselect.refreshOptions(false);
                    if (keepValue) selDiv.tomselect.setValue(keepValue);
                } else {
                    list.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.id;
                        opt.textContent = d.nama_divisi;
                        selDiv.appendChild(opt);
                    });
                    if(keepValue) selDiv.value = keepValue;
                }
                // allow change handlers again
                suppressFilterApply = false;
            }catch(e){ console.error(e); suppressFilterApply = false; }
        }

        async function populateUnits(divisiId, keepValue){
            if(!selUnit) return;
            if (selUnit.tomselect) {
                selUnit.tomselect.clearOptions();
                selUnit.tomselect.addOption([{value: '', text: 'Semua Unit'}]);
            } else {
                selUnit.innerHTML = '<option value="">Semua Unit</option>';
            }
            if(!divisiId) return;
            try{
                suppressFilterApply = true;
                const url = unitUrlTmpl.replace('__ID__', encodeURIComponent(divisiId));
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const list = await res.json();
                if (selUnit.tomselect) {
                    list.forEach(u => selUnit.tomselect.addOption({value: u.id, text: u.nama_unit}));
                    selUnit.tomselect.refreshOptions(false);
                    if (keepValue) selUnit.tomselect.setValue(keepValue);
                } else {
                    list.forEach(u => {
                        const opt = document.createElement('option');
                        opt.value = u.id;
                        opt.textContent = u.nama_unit;
                        selUnit.appendChild(opt);
                    });
                    if(keepValue) selUnit.value = keepValue;
                }
                suppressFilterApply = false;
            }catch(e){ console.error(e); suppressFilterApply = false; }
        }

        // Dependent behavior
        if(selDir){
            selDir.addEventListener('change', async (e)=>{
                if (suppressFilterApply) return;
                const d = e.target.value;
                await populateDivisis(d);
                applyFilters();
            });
        }
        if(selDiv){
            selDiv.addEventListener('change', async (e)=>{
                if (suppressFilterApply) return;
                const v = e.target.value;
                await populateUnits(v);
                applyFilters();
            });
        }
        if(selUnit){ selUnit.addEventListener('change', (e)=>{ if(!suppressFilterApply) applyFilters(); }); }

        // On first load, if there is a selected direktorat/divisi via query, ensure dependent dropdowns are fully populated
        (async function initDependents(){
            const currentDir = selDir?.value || '';
            const currentDivSelected = "{{ $divId ?? '' }}";
            const currentUnitSelected = "{{ $unitId ?? '' }}";
            if(currentDir){
                await populateDivisis(currentDir, currentDivSelected);
            }
            if(currentDivSelected){
                await populateUnits(currentDivSelected, currentUnitSelected);
            }
        })();

        // delegate row clicks
        IS.attachRowClicks();
    })();
    </script>
@endpush
