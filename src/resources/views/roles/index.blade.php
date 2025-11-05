@extends('layouts.master')

@section('title', 'Roles')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Roles</h1>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center">
                <div class="font-semibold">Daftar Role</div>
                @can('create role')
                    <a href="{{ route('roles.create') }}" class="inline-flex items-center px-3 py-2 rounded bg-purple-600 text-white text-sm">Tambah Role</a>
                @endcan
            </div>
            <div class="overflow-auto">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">No</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Nama Role</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500"># Permissions</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach($roles as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $r->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $r->permissions_count }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                @can('update role')
                                    <x-action-button type="edit" href="{{ route('roles.edit', $r->id) }}" color="purple" />
                                @endcan
                                @can('delete role')
                                    <x-action-button type="delete" action="{{ route('roles.destroy', $r->id) }}" color="red" confirm="Hapus role ini?" />
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection
