@extends('layouts.master')

@section('title', 'Team Approvals')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Team Approvals</h1>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg p-6">

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b">
              <th class="text-left p-2">#</th>
              <th class="text-left p-2">Employee</th>
              <th class="text-left p-2">Jabatan</th>
              <th class="text-left p-2">Platform</th>
              <th class="text-left p-2">Title</th>
              <th class="text-left p-2">Duration</th>
              <th class="text-left p-2">Submitted</th>
              <th class="text-left p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($logs as $i => $log)
            <tr class="border-b">
              <td class="p-2">{{ $logs->firstItem() + $i }}</td>
              <td class="p-2">{{ $log->owner->nama ?? '-' }}</td>
              <td class="p-2">{{ $log->owner->jabatan->nama_jabatan ?? '-' }}</td>
              <td class="p-2">{{ $log->platform->name ?? '-' }}</td>
              <td class="p-2">{{ $log->title }}</td>
              <td class="p-2">{{ $log->duration_minutes }} mins</td>
              <td class="p-2">{{ $log->submitted_at }}</td>
              <td class="p-2 space-x-2">
                @can('approve learning log')
                  <form method="POST" action="{{ route('learning.logs.approve',$log) }}" class="inline">
                    @csrf
                    <button class="text-green-700">Approve</button>
                  </form>
                  <form method="POST" action="{{ route('learning.logs.reject',$log) }}" class="inline">
                    @csrf
                    <input type="hidden" name="reason" value="Insufficient detail" />
                    <button class="text-red-700">Reject</button>
                  </form>
                @endcan
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $logs->links() }}</div>
    </div>
@endsection
