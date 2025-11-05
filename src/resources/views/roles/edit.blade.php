@extends('layouts.master')

@section('title', 'Edit Role')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Edit Role</h1>
@endsection

@section('content')
    <form action="{{ route('roles.update', $role->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-xl shadow p-4">
            <div class="mb-4">
                <label class="block text-sm text-gray-600">Nama Role</label>
                <input type="text" name="name" value="{{ $role->name }}" class="mt-1 w-full rounded border-gray-200" required>
            </div>
            <div>
                <div class="font-semibold mb-2">Permissions</div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-96 overflow-auto p-2 border rounded">
                    @foreach($permissions as $p)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $p->name }}" {{ in_array($p->name, $rolePermissions) ? 'checked' : '' }}> {{ $p->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('roles.index') }}" class="px-3 py-2 rounded bg-gray-100">Batal</a>
                <button type="submit" class="px-3 py-2 rounded bg-purple-600 text-white">Simpan</button>
            </div>
        </div>
    </form>

    {{-- Users assignment form --}}
    <div class="mt-6 bg-white rounded-xl shadow p-4">
        <h3 class="font-semibold mb-3">Assign Users to Role</h3>
        <form action="{{ route('roles.users.sync', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            <label class="block text-sm text-gray-600 mb-2">Users</label>
            <select name="users[]" multiple size="10" class="w-full rounded border-gray-200">
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ in_array($u->id, $roleUserIds) ? 'selected' : '' }}>{{ $u->name }} &lt;{{ $u->email }}&gt;</option>
                @endforeach
            </select>
            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('roles.index') }}" class="px-3 py-2 rounded bg-gray-100">Batal</a>
                <button type="submit" class="px-3 py-2 rounded bg-green-600 text-white">Sync Users</button>
            </div>
        </form>
    </div>
@endsection
