@extends('layouts.master')

@section('title', 'Learning Log Detail')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Learning Log Detail</h1>
@endsection

@section('content')
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
      <h1 class="text-xl font-semibold mb-4">Learning Log Detail</h1>
      <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><dt class="font-medium">Platform</dt><dd>{{ $log->platform->name ?? '-' }}</dd></div>
        <div><dt class="font-medium">Title</dt><dd>{{ $log->title }}</dd></div>
        <div><dt class="font-medium">Duration</dt><dd>{{ $log->duration_minutes }} mins</dd></div>
        <div><dt class="font-medium">Status</dt><dd class="capitalize">{{ $log->status }}</dd></div>
        <div class="md:col-span-2"><dt class="font-medium">Description</dt><dd>{{ $log->description ?? '-' }}</dd></div>
        <div class="md:col-span-2"><dt class="font-medium">Evidence</dt><dd>@if($log->evidence_url)<a href="{{ $log->evidence_url }}" class="text-blue-600" target="_blank">Open evidence</a>@else - @endif</dd></div>
      </dl>

      <h2 class="text-lg font-semibold mt-6 mb-2">Activities</h2>
      <ul class="list-disc pl-6">
        @forelse($log->activities as $act)
          <li>{{ $act->created_at }} - {{ $act->action }} by {{ $act->actor->name ?? 'System' }}</li>
        @empty
          <li>No activities yet.</li>
        @endforelse
      </ul>
    </div>
@endsection
