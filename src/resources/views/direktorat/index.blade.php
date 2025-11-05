@extends('layouts.master')

@section('title', 'Direktorat')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Direktorat</h1>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">Direktorat</div>
                    @can('create direktorat')
                        <a href="{{ route('direktorat.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Tambah Direktorat</a>
                    @endcan
                </div>
            </div>
            <div class="overflow-auto">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">No</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Nama</th>
                            @can('update direktorat')
                                <th class="px-4 py-2 text-left text-xs text-gray-500">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach($direktorats as $d)
                        <tr class="hover:bg-gray-50 cursor-pointer" data-href="{{ route('direktorat.show', $d->id) }}">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($direktorats->currentPage() - 1) * $direktorats->perPage() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <a href="{{ route('direktorat.show', $d->id) }}" class="text-purple-600">{{ $d->nama_direktorat }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                @can('update direktorat')
                                    <x-action-button type="edit" href="{{ route('direktorat.edit', $d->id) }}" color="purple" />
                                @endcan
                                @can('delete direktorat')
                                    <x-action-button type="delete" action="{{ route('direktorat.destroy', $d->id) }}" color="red" confirm="Hapus direktorat ini?" />
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
                <div class="mt-4">
                {{ $direktorats->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('tr[data-href]').forEach(function (tr) {
            tr.style.cursor = 'pointer';
            tr.addEventListener('click', function (e) {
                // don't navigate when clicking an actionable element inside row
                if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) return;
                var href = tr.getAttribute('data-href');
                if (href) window.location.href = href;
            });
        });
    });
</script>
@endpush
