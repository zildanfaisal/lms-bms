@extends('layouts.master')

@section('title', 'Posisi')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Posisi</h1>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">Posisi</div>
                    <div class="flex items-center gap-3">
                        <input id="posisi-search" type="text" placeholder="Cari posisi..." value="{{ request('q', '') }}" class="rounded border-gray-200 px-3 py-2 text-sm">
                        <x-action-button type="reset" id="posisi-reset" class="hidden inline-flex items-center px-3 py-2 rounded bg-red-100 text-sm text-red-600" />
                        <a href="{{ route('posisi.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Tambah Posisi</a>
                    </div>
                </div>
            </div>
            <div class="overflow-auto">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">No</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Nama Posisi</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach($posisis as $d)
                        <tr class="hover:bg-gray-50" data-href="{{ route('posisi.edit', $d->id) }}">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($posisis->currentPage() - 1) * $posisis->perPage() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <a href="{{ route('posisi.edit', $d->id) }}" class="text-purple-600" onclick="event.stopPropagation();">{{ $d->nama_posisi }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                <x-action-button type="edit" href="{{ route('posisi.edit', $d->id) }}" color="purple" />
                                <x-action-button type="delete" action="{{ route('posisi.destroy', $d->id) }}" color="red" confirm="Hapus posisi ini?" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
                <div class="mt-4">
                {{ $posisis->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('components.instant-search-scripts')
    <script>
    (function(){
        const input = document.getElementById('posisi-search');
        const resetBtn = document.getElementById('posisi-reset');
        const tbody = document.querySelector('table tbody');
        const pagination = document.querySelector('.mt-4');
        async function doSearch(q){
            if(!q){ resetBtn.classList.add('hidden'); location.search = ''; return; }
            resetBtn.classList.remove('hidden');
            try{
                const res = await fetch(`{{ route('posisi.index') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                tbody.innerHTML = data.map((item, idx) => `\
                    <tr class="hover:bg-gray-50" data-href="/posisi/${item.id}">\
                        <td class="px-4 py-3 text-sm text-gray-700">${idx+1}</td>\
                        <td class="px-4 py-3 text-sm text-gray-700">\
                            <a href="/posisi/${item.id}" class="text-purple-600" onclick="event.stopPropagation();">${IS.escape(item.nama_posisi)}</a>\
                        </td>\
                        <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">\
                            ${IS.renderEdit(`/posisi/${item.id}/edit`, 'text-purple-600')}\
                            ${IS.renderDelete(`/posisi/${item.id}`, 'Hapus posisi ini?', 'text-red-600')}\
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
