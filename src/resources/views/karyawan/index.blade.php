@extends('layouts.master')

@section('title', 'Karyawan')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Karyawan</h1>
@endsection

@section('content')
    <div id="page" data-can-edit="{{ auth()->user()->can('update karyawan') ? 1 : 0 }}" data-can-delete="{{ auth()->user()->can('delete karyawan') ? 1 : 0 }}" class="bg-white rounded-xl shadow overflow-hidden">
                <div class="p-4 border-b">
            <div class="flex justify-between items-center">
                <div class="font-semibold">Karyawan</div>
                <div class="flex items-center gap-3">
                    <input id="karyawan-search" type="text" name="q" value="{{ request('q', '') }}" placeholder="Cari NIK, nama, email, unit..." class="rounded border-gray-200 px-3 py-2 text-sm">
                    <x-action-button type="reset" id="karyawan-reset" class="hidden inline-flex items-center px-3 py-2 rounded bg-red-100 text-sm text-red-600">Reset</x-action-button>
                    @can('create karyawan')
                        <a href="{{ route('karyawan.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-green-600 text-white text-sm">Tambah Karyawan</a>
                    @endcan
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

        async function doSearch(q){
            if(!q){ resetBtn.classList.add('hidden'); location.search = ''; return; }
            resetBtn.classList.remove('hidden');
            try{
                const res = await fetch(`{{ route('karyawan.index') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } });
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
        if(resetBtn){ resetBtn.addEventListener('click', ()=>{ input.value=''; input.dispatchEvent(new Event('input')); }); }

        // delegate row clicks
        IS.attachRowClicks();
    })();
    </script>
@endpush
