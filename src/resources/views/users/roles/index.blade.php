@extends('layouts.master')

@section('title', 'User Roles')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800">Manage User Roles</h1>
    </div>
@endsection

@section('content')
    <div class="space-y-6">    
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <div class="font-semibold">Daftar Role</div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <input id="searchInput" type="text" value="{{ $q ?? '' }}" placeholder="Cari nama/email" class="rounded border-gray-200 pr-8" />
                        <svg id="searchSpinner" class="hidden animate-spin h-4 w-4 absolute right-2 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-sm text-gray-600">
                        <th class="p-3">No</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Role Sekarang</th>
                        <th class="p-3">Ubah Role</th>
                    </tr>
                </thead>
                <tbody id="rows" class="divide-y">
                    @forelse($users as $u)
                        <tr class="text-sm">
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">{{ $u->name }}</td>
                            <td class="p-3">{{ $u->email }}</td>
                            <td class="p-3">
                                @php($names = $u->roles->pluck('name'))
                                @if($names->isEmpty())
                                    <span class="text-gray-400">(none)</span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($names as $n)
                                            <span class="inline-block px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">{{ $n }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="p-3">
                                <form action="{{ route('users.roles.update', $u->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="min-w-[220px] rounded border-gray-200">
                                        <option value="">— No role —</option>
                                        @foreach($roles as $r)
                                            <option value="{{ $r->name }}" {{ $u->roles->contains('name', $r->name) ? 'selected' : '' }}>{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-3 py-2 rounded bg-green-600 text-white">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr id="noResults">
                            <td colspan="5" class="p-6 text-center text-gray-500">Tidak ada user</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">{{ $users->links() }}</div>
        </div>
    </div>
@endsection

@include('components.instant-search-scripts')

{{-- embed roles list as JSON to avoid inline Blade inside JS codeblocks --}}
<script id="roles-data" type="application/json">{!! $roles->pluck('name')->values()->toJson() !!}</script>

@push('scripts')
<script>
    (function(){
        const input = document.getElementById('searchInput');
        const spinner = document.getElementById('searchSpinner');
        const tbody = document.getElementById('rows');
        // roles JSON is provided in a separate script tag to avoid editor/JS parser issues
        const rolesNode = document.getElementById('roles-data');
        const roles = rolesNode ? JSON.parse(rolesNode.textContent || '[]') : [];

        function renderBadges(roleNames){
            if(!roleNames || !roleNames.length){
                return '<span class="text-gray-400">(none)</span>';
            }
            return '<div class="flex flex-wrap gap-1">' + roleNames.map(function(n){
                return '<span class="inline-block px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">' + IS.escape(n) + '</span>';
            }).join('') + '</div>';
        }

        function renderRow(item, idx){
            const action = '/users/' + item.id + '/roles';
            const options = ['<option value="">— No role —</option>'].concat(roles.map(function(r){
                const sel = (item.selectedRole === r) ? ' selected' : '';
                return '<option value="' + IS.escape(r) + '"' + sel + '>' + IS.escape(r) + '</option>';
            })).join('');
            return '<tr class="text-sm">'
                + '<td class="p-3">' + (idx + 1) + '</td>'
                + '<td class="p-3">' + IS.escape(item.name) + '</td>'
                + '<td class="p-3">' + IS.escape(item.email) + '</td>'
                + '<td class="p-3">' + renderBadges(item.roles) + '</td>'
                + '<td class="p-3">'
                    + '<form action="' + action + '" method="POST" class="flex items-center gap-2">'
                        + IS._csrfInput
                        + '<input type="hidden" name="_method" value="PUT">'
                        + '<select name="role" class="min-w-[220px] rounded border-gray-200">' + options + '</select>'
                        + '<button type="submit" class="px-3 py-2 rounded bg-green-600 text-white">Simpan</button>'
                    + '</form>'
                + '</td>'
                + '<td class="p-3 text-right"></td>'
            + '</tr>';
        }

        async function search(q){
            spinner.classList.remove('hidden');
            try{
                const res = await fetch('/users/roles?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                const items = data.items || [];
                if(items.length === 0){
                    tbody.innerHTML = '<tr id="noResults"><td colspan="5" class="p-6 text-center text-gray-500">Tidak ada hasil</td></tr>';
                } else {
                    tbody.innerHTML = items.map(function(it, i){ return renderRow(it, i); }).join('');
                }
            } catch(e){
                console.error(e);
            } finally {
                spinner.classList.add('hidden');
            }
        }

        input.addEventListener('input', IS.debounce(function(ev){
            const q = ev.target.value || '';
            if(q.trim() === ''){
                // optional: reload page to server-rendered state
                // location.href = location.pathname;
                search('');
            } else {
                search(q);
            }
        }, 300));
    })();
</script>
@endpush
