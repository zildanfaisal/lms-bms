@extends('layouts.master')

@section('title', 'Detail Direktorat')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Detail Direktorat</h1>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Direktorat</div>
                        <div class="text-lg font-semibold">{{ $direktorat->nama_direktorat }}</div>
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-gray-500">Jumlah Divisi</div>
                        <div class="text-lg font-semibold">{{ $countDivisi }}</div>
                        @can('create divisi')
                        <div class="mt-2">
                            <a href="{{ route('divisi.create', ['direktorat_id' => $direktorat->id]) }}" class="inline-flex items-center px-3 py-2 rounded bg-green-600 text-white text-sm">Tambah Divisi</a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="p-4">
                <h3 class="text-sm font-semibold mb-2">Daftar Divisi</h3>
                <div class="overflow-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">No</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">Nama Divisi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @foreach($divisis as $div)
                                <tr class="hover:bg-gray-50" data-href="{{ route('divisi.show', $div->id) }}">
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($divisis->currentPage() - 1) * $divisis->perPage() }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <a href="{{ route('divisi.show', $div->id) }}" class="text-purple-600" onclick="event.stopPropagation();">{{ $div->nama_divisi }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $divisis->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function(e) {
    const row = e.target.closest('tr[data-href]');
    if (!row) return;
    // Skip navigation when clicking interactive elements
    if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) return;
    window.location = row.getAttribute('data-href');
});
</script>
@endpush
