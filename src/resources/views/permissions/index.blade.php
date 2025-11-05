@extends('layouts.master')

@section('title', 'Permissions')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Permissions</h1>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center">
                <div class="font-semibold">Daftar Permission</div>
                @can('create permission')
                    <a href="{{ route('permissions.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Tambah Permission</a>
                @endcan
            </div>
            <div class="overflow-auto">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">No</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Nama</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach($permissions as $p)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($permissions->currentPage() - 1) * $permissions->perPage() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $p->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                @can('delete permission')
                                    <x-action-button type="delete" action="{{ route('permissions.destroy', $p->id) }}" color="red" confirm="Hapus permission ini?" />
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
@endsection
