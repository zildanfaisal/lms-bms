@extends('layouts.master')

@section('title', 'Divisi')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Divisi</h1>
@endsection

@section('content')
    <div id="page" data-can-edit="{{ auth()->user()->can('update divisi') ? 1 : 0 }}" data-can-delete="{{ auth()->user()->can('delete divisi') ? 1 : 0 }}" class="space-y-6">
        {{-- Table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">Divisi</div>
                    <div class="flex items-center gap-3">
                        <input id="divisi-search" type="text" placeholder="Cari divisi atau direktorat..." value="{{ request('q', '') }}" class="rounded border-gray-200 px-3 py-2 text-sm">
                        <x-action-button type="reset" id="divisi-reset" class="hidden inline-flex items-center px-3 py-2 rounded bg-red-100 text-sm text-red-600" />
                        @can('create divisi')
                            <a href="{{ route('divisi.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Tambah Divisi</a>
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
                            @can('update divisi')
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach($divisis as $d)
                        <tr class="hover:bg-gray-50" data-href="{{ route('divisi.show', $d->id) }}">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($divisis->currentPage() - 1) * $divisis->perPage() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <a href="{{ route('divisi.show', $d->id) }}" class="text-purple-600">{{ $d->nama_divisi }}</a>
                                <div class="text-xs text-gray-400">{{ $d->direktorat?->nama_direktorat }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                @can('update divisi')
                                    <x-action-button type="edit" href="{{ route('divisi.edit', $d->id) }}" color="purple" />
                                @endcan
                                @can('delete divisi')
                                    <x-action-button type="delete" action="{{ route('divisi.destroy', $d->id) }}" color="red" confirm="Hapus divisi ini?" precheck="{{ route('divisi.precheck-delete', $d->id) }}" />
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
                <div class="mt-4">
                {{ $divisis->links() }}
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
        const input = document.getElementById('divisi-search');
        const resetBtn = document.getElementById('divisi-reset');
        const tbody = document.querySelector('table tbody');
        const pagination = document.querySelector('.mt-4');

        async function doSearch(q){
            if(!q){ resetBtn.classList.add('hidden'); location.search = ''; return; }
            resetBtn.classList.remove('hidden');
            try{
                const res = await fetch(`{{ route('divisi.index') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                tbody.innerHTML = data.map((item, idx) => `\
                    <tr class="hover:bg-gray-50" data-href="/divisi/${item.id}">\
                        <td class="px-4 py-3 text-sm text-gray-700">${idx+1}</td>\
                        <td class="px-4 py-3 text-sm text-gray-700">\
                            <a href="/divisi/${item.id}" class="text-purple-600">${IS.escape(item.nama_divisi)}</a>\
                            <div class="text-xs text-gray-400">${IS.escape(item.nama_direktorat || '')}</div>\
                        </td>\
                        <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">\
                            ${CAN_EDIT ? IS.renderEdit(`/divisi/${item.id}/edit`, 'text-purple-600') : ''}\
                            ${CAN_DELETE ? IS.renderDelete(`/divisi/${item.id}`, 'Hapus divisi ini?', 'text-red-600', 'DELETE', `/divisi/${item.id}/precheck-delete`) : ''}\
                        </td>\
                    </tr>`).join('');
                if(pagination) pagination.style.display = 'none';
            }catch(e){ console.error(e); }
        }

        const deb = IS.debounce(e => doSearch(e.target.value), 300);
        if(input){ input.addEventListener('input', deb); }
        if(resetBtn){ resetBtn.addEventListener('click', ()=>{ input.value=''; input.dispatchEvent(new Event('input')); }); }

        // row click delegation
        IS.attachRowClicks();
    })();
    </script>
@endpush
