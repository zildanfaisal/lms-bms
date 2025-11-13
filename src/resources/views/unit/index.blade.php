@extends('layouts.master')

@section('title', 'Unit')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Unit</h1>
@endsection

@section('content')
    <div id="page" data-can-edit="{{ auth()->user()->can('update unit') ? 1 : 0 }}" data-can-delete="{{ auth()->user()->can('delete unit') ? 1 : 0 }}" class="space-y-6">
        {{-- Table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">Unit</div>
                    <div class="flex items-center gap-3">
                        <input id="unit-search" type="text" placeholder="Cari unit atau divisi..." value="{{ request('q', '') }}" class="rounded border-gray-200 px-3 py-2 text-sm">
                        <x-action-button type="reset" id="unit-reset" class="hidden inline-flex items-center px-3 py-2 rounded bg-red-100 text-sm text-red-600" />
                        @can('create unit')
                            <a href="{{ route('unit.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Tambah Unit</a>
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
                            @can('update unit')
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach($units as $d)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($units->currentPage() - 1) * $units->perPage() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $d->nama_unit }}
                                <div class="text-xs text-gray-400">{{ $d->divisi?->nama_divisi }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                @can('update unit')
                                    <x-action-button type="edit" href="{{ route('unit.edit', $d->id) }}" color="purple" />
                                @endcan
                                @can('delete unit')
                                    <x-action-button type="delete" action="{{ route('unit.destroy', $d->id) }}" color="red" confirm="Hapus unit ini?" precheck="{{ route('unit.precheck-delete', $d->id) }}" />
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
                <div class="mt-4">
                {{ $units->links() }}
            </div>
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
        const input = document.getElementById('unit-search');
        const resetBtn = document.getElementById('unit-reset');
        const tbody = document.querySelector('table tbody');
        const pagination = document.querySelector('.mt-4');
        async function doSearch(q){
            if(!q){ resetBtn.classList.add('hidden'); location.search = ''; return; }
            resetBtn.classList.remove('hidden');
            try{
                const res = await fetch(`{{ route('unit.index') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                tbody.innerHTML = data.map((item, idx) => `\
                    <tr>\
                        <td class="px-4 py-3 text-sm text-gray-700">${idx+1}</td>\
                        <td class="px-4 py-3 text-sm text-gray-700">\
                            ${IS.escape(item.nama_unit)} <div class=\"text-xs text-gray-400\">${IS.escape(item.nama_divisi || '')}</div>\
                        </td>\
                        <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">\
                            ${CAN_EDIT ? IS.renderEdit(`/unit/${item.id}/edit`, 'text-purple-600') : ''}\
                            ${CAN_DELETE ? IS.renderDelete(`/unit/${item.id}`, 'Hapus unit ini?', 'text-red-600', 'DELETE', `/unit/${item.id}/precheck-delete`) : ''}\
                        </td>\
                    </tr>`).join('');
                if(pagination) pagination.style.display = 'none';
            }catch(e){ console.error(e); }
        }
        const deb = IS.debounce(e => doSearch(e.target.value), 300);
        if(input){ input.addEventListener('input', deb); }
        if(resetBtn){ resetBtn.addEventListener('click', ()=>{ input.value=''; input.dispatchEvent(new Event('input')); }); }
        IS.attachRowClicks();
    })();
    </script>
@endpush
