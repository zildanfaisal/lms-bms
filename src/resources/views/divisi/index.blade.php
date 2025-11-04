@extends('layouts.master')

@section('title', 'Divisi')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800">Divisi</h1>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <div class="font-semibold">Divisi</div>
                    <a href="#" class="text-sm text-purple-600">Lihat Semua</a>
                </div>
            </div>
            <div class="p-4">
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
                            @foreach($divisis as $d)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration + ($divisis->currentPage() - 1) * $divisis->perPage() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $d->nama_divisi }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 flex gap-2">
                                    <a href="#" class="text-purple-600">Edit</a>
                                    <a href="#" class="text-red-600">Delete</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                 <div class="mt-4">
                    {{ $divisis->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
