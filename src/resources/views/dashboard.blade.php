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
            <div class="bg-white rounded-xl p-5 shadow-md flex items-center gap-4 md:col-span-1">
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6z"></path><path d="M3 11h14v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5z"></path></svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Periode Saat Ini</div>
                    <div class="text-lg font-semibold text-indigo-600">{{ $learningPeriod->name ?? '-' }}</div>
                </div>
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

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">Table 1</div>
                    <a href="#" class="text-sm text-purple-600">Lihat Semua</a>
                </div>
            </div>
            <div class="p-4">
                <div class="overflow-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">NO</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">#</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">#</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">#</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">#</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @for ($i = 1; $i <= 5; $i++)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $i }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">Row {{ ['Pertama','Kedua','Ketiga','Keempat','Kelima'][$i-1] ?? 'Session' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">#</td>
                                <td class="px-4 py-3 text-sm text-gray-700">#</td>
                                <td class="px-4 py-3 text-sm text-gray-700">#</td>
                                <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                    <a href="#" class="text-purple-600">✏️</a>
                                    <a href="#" class="text-red-600">🗑️</a>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
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
