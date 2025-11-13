@extends('layouts.master')

@section('title', 'Learning Platforms')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Learning Platforms</h1>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg p-6">

  <form method="POST" action="{{ route('learning.platforms.store') }}" class="mb-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <input name="name" placeholder="Name" class="border rounded p-2" required />
          <select name="type" class="border rounded p-2">
            <option value="internal">Internal</option>
            <option value="external">External</option>
          </select>
          <input name="url" placeholder="URL" class="border rounded p-2" />
          <input name="description" placeholder="Description" class="border rounded p-2" />
          <button class="px-4 py-2 bg-blue-600 text-white rounded">Add</button>
        </div>
      </form>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">#</th>
              <th class="text-left p-2">Name</th>
              <th class="text-left p-2">Type</th>
              <th class="text-left p-2">URL</th>
              <th class="text-left p-2">Active</th>
              <th class="text-left p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($platforms as $i => $p)
              <tr class="border-b">
                <td class="p-2">{{ $platforms->firstItem() + $i }}</td>
                <td class="p-2">{{ $p->name }}</td>
                <td class="p-2">{{ ucfirst($p->type) }}</td>
                <td class="p-2">@if($p->url)<a href="{{ $p->url }}" class="text-blue-600" target="_blank">Open</a>@endif</td>
                <td class="p-2">{{ $p->is_active ? 'Yes' : 'No' }}</td>
                <td class="p-2">
                  <form method="POST" action="{{ route('learning.platforms.destroy',$p) }}" data-confirm="Hapus platform ini?">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-700">Delete</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $platforms->links() }}</div>
    </div>
@endsection
