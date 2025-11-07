@extends('layouts.master')

@section('title', 'Dashboard')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Top info bar like screenshot --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-5 shadow-md flex items-center gap-4 md:col-span-2">
                <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-indigo-700 font-semibold text-lg">{{ $profileInitials }}</span>
                </div>
                <div class="min-w-0">
                    <div class="text-lg font-semibold text-gray-800 truncate">{{ $profileName }}</div>
                    <div class="mt-1 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1 text-sm text-gray-600">
                        <div><span class="text-gray-400">Direktorat:</span> {{ $profileDirektorat }}</div>
                        <div><span class="text-gray-400">Jabatan:</span> {{ $profileJabatan }}</div>
                        <div><span class="text-gray-400">Divisi:</span> {{ $profileDivisi }}</div>
                        <div><span class="text-gray-400">Posisi:</span> {{ $profilePosisi }}</div>
                        <div><span class="text-gray-400">Unit:</span> {{ $profileUnit }}</div>
                    </div>
                </div>
            </div>
                        <div class="bg-white rounded-xl p-5 shadow-md md:col-span-1">
                            <form method="GET" action="{{ route('dashboard') }}" class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6z"></path><path d="M3 11h14v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                            <div class="text-sm text-gray-500">Periode</div>
                                            <select name="period_id" class="mt-1 w-full rounded tom-select">
                                                <option value="">(Aktif Otomatis)</option>
                                                @foreach($periodOptions as $p)
                                                    <option value="{{ $p->id }}" @selected($selectedPeriodId == $p->id)>{{ $p->name }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button class="px-3 py-1.5 rounded bg-indigo-600 text-white text-xs">Ganti</button>
                                </div>
                            </form>
                        </div>
        </div>

        {{-- Stat cards grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-5 shadow-md flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6z"></path><path d="M3 11h14v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5z"></path></svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Target Belajar (menit)</div>
                    <div class="text-lg font-semibold text-indigo-600">{{ $learningTargetMinutes ?? '-' }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md flex items-center gap-4 border border-yellow-50">
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H9z"></path><path d="M3 11h14v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5z"></path></svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Approved Minutes</div>
                    <div class="text-lg font-semibold text-yellow-600">{{ $learningApprovedMinutes ?? 0 }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H10z"></path><path d="M3 11h14v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5z"></path></svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Progress</div>
                    @php
                        $t = $learningTargetMinutes ?? 0;
                        $m = $learningApprovedMinutes ?? 0;
                        $pct = $t ? min(100, ($m / max(1,$t)) * 100) : null;
                    @endphp
                    <div class="text-lg font-semibold text-green-600">{{ $pct !== null ? number_format($pct,0).'%' : '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Recommendations for the user --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">Rekomendasi untuk Anda</div>
                </div>
            </div>
            <div class="p-4">
                @if (!empty($recommendedItems))
                    <ul class="space-y-3">
                        @foreach ($recommendedItems as $idx => $item)
                            <li class="flex items-start gap-3">
                                <div class="mt-1 w-2 h-2 rounded-full bg-indigo-500"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <div class="text-sm font-medium text-gray-800 truncate">{{ $item['title'] }}</div>
                                        @if(isset($item['scope_type']))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border">
                                                {{ ucfirst($item['scope_type']) }}
                                                @if($item['jabatan_id'])<span class="ml-1 text-gray-400">+Jabatan</span>@endif
                                            </span>
                                        @endif
                                        @if(!empty($item['approved_proposal_id']) && $canReviewProposals)
                                            <a href="{{ route('learning.reviews.show', $item['approved_proposal_id']) }}" class="text-[10px] text-indigo-600 hover:underline">#P{{ $item['approved_proposal_id'] }}</a>
                                        @endif
                                        @php($platformName = $item['platform_id'] ? ($platformMap[$item['platform_id']] ?? null) : null)
                                        @if($platformName)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                {{ $platformName }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 flex items-center gap-3 flex-wrap">
                                        @if (!empty($item['url']))
                                            @if(!empty($item['id']))
                                                <a href="{{ route('learning.recommendations.click', $item['id']) }}" class="text-xs text-indigo-600 hover:underline break-all" target="_blank">Buka</a>
                                            @else
                                                <a href="{{ $item['url'] }}" target="_blank" class="text-xs text-indigo-600 hover:underline break-all">Buka</a>
                                            @endif
                                        @endif
                                        <a href="{{ route('learning.logs.index', ['title' => $item['title'] ?? null, 'platform_id' => $item['platform_id'] ?? null, 'evidence_url' => $item['url'] ?? null]) }}" class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700">Catat</a>
                                    </div>
                                </div>
                            </li>
                            @if ($idx >= 9)
                                @break
                            @endif
                        @endforeach
                    </ul>
                @else
                    <div class="text-sm text-gray-500">Belum ada rekomendasi belajar untuk periode ini.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}
