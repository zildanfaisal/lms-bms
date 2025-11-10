@extends('layouts.master')

@section('title', 'Catat Pembelajaran')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Catat Pembelajaran</h1>
@endsection

@section('content')
  <div class="bg-white shadow-sm sm:rounded-lg p-6">
    @if (session('status'))
      <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
      <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('learning.logs.store') }}" class="mb-6">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
        <div>
          <label class="block text-sm font-medium">Periode</label>
          <select name="period_id" class="mt-1 w-full border rounded p-2" required>
            <option value="">-- Pilih Periode --</option>
            @foreach($periodOptions as $p)
              <option value="{{ $p->id }}" @selected( (string)$p->id === (string)old('period_id', $prefill['period_id'] ?? '') )>{{ $p->name }}</option>
            @endforeach
          </select>
          @error('period_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">Platform</label>
          <select name="platform_id" class="mt-1 w-full border rounded p-2" required>
            <option value="">-- Select Platform --</option>
            @foreach($platforms as $p)
              <option value="{{ $p->id }}" @selected( (string)$p->id === (string)old('platform_id', $prefill['platform_id'] ?? '') )>{{ $p->name }}</option>
            @endforeach
          </select>
          @error('platform_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium">Title</label>
          <input name="title" type="text" value="{{ old('title', $prefill['title'] ?? '') }}" class="mt-1 w-full border rounded p-2" required />
          @error('title')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium">Start Date</label>
          <input name="started_at" type="date" value="{{ old('started_at', $prefill['started_at'] ?? '') }}" class="mt-1 w-full border rounded p-2" required />
          @error('started_at')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium">End Date</label>
          <input name="ended_at" type="date" value="{{ old('ended_at', $prefill['ended_at'] ?? '') }}" class="mt-1 w-full border rounded p-2" required />
          @error('ended_at')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium">Duration (minutes)</label>
          <input name="duration_minutes" type="number" min="1" step="1" value="{{ old('duration_minutes', $prefill['duration_minutes'] ?? '') }}" class="mt-1 w-full border rounded p-2" required />
          @error('duration_minutes')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-medium">Learning URL</label>
          <input name="learning_url" type="url" value="{{ old('learning_url', $prefill['learning_url'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
          @error('learning_url')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-medium">Evidence URL (optional)</label>
          <input name="evidence_url" type="url" value="{{ old('evidence_url', $prefill['evidence_url'] ?? '') }}" class="mt-1 w-full border rounded p-2" />
          @error('evidence_url')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="md:col-span-6">
          <label class="block text-sm font-medium">Description</label>
          <textarea name="description" class="mt-1 w-full border rounded p-2">{{ old('description') }}</textarea>
          @error('description')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <input type="hidden" name="recommendation_id" value="{{ old('recommendation_id', $prefill['recommendation_id'] ?? '') }}" />
        <div>
          <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Draft</button>
        </div>
      </div>
    </form>
  </div>
@endsection
