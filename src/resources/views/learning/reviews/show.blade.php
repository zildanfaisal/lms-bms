@extends('layouts.master')

@section('title', 'Detail Usulan Rencana')

@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Detail Usulan Rencana</h1>
@endsection

@section('content')
  <div class="bg-white rounded-xl shadow p-4 space-y-4">
    @if (session('status'))
      <div class="p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
      <div class="p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-sm text-gray-500">Periode</div>
        <div class="font-semibold">{{ $proposal->period->name ?? '-' }}</div>
      </div>
      <div>
        <div class="text-sm text-gray-500">Pengusul</div>
        <div class="font-semibold">{{ $proposal->proposer->name ?? '-' }}</div>
      </div>
      <div>
        <div class="text-sm text-gray-500">Scope</div>
        <div class="font-semibold">{{ ucfirst($proposal->scope_type) }} — {{ $scopeName ?? ('#'.$proposal->scope_id) }}</div>
        @if($proposal->scope_type==='unit' && $proposal->only_subordinate_jabatans)
          <div class="text-xs text-indigo-600 mt-1">Target & rekomendasi hanya untuk jabatan bawahan</div>
        @endif
      </div>
      <div>
        <div class="text-sm text-gray-500">Target Menit</div>
        <div class="font-semibold">{{ $proposal->target_minutes ?? '-' }}</div>
      </div>
      <div>
        <div class="text-sm text-gray-500">Catatan</div>
        <div class="font-semibold">{{ $proposal->notes ?? '-' }}</div>
      </div>
    </div>

    <div class="space-y-2">
      <div class="text-sm text-gray-500">Rekomendasi</div>
      <ul class="list-disc ml-5 mt-1">
        @forelse($proposal->recommendations as $r)
          <li>
            <span class="font-medium">{{ $r->title }}</span>
            @if($r->url)
              — <a href="{{ $r->url }}" target="_blank" class="text-indigo-600">Tautan</a>
            @endif
          </li>
        @empty
          <li class="text-gray-500">Tidak ada rekomendasi</li>
        @endforelse
      </ul>
    </div>

    @if(!empty($jabatanRows))
      <div class="space-y-2">
        <div class="text-sm text-gray-500">Dampak (Jabatan Bawahan & Jumlah Karyawan)</div>
        <table class="min-w-full text-xs border">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-2">Jabatan</th>
              <th class="text-left p-2">Jumlah Karyawan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($jabatanRows as $row)
              <tr class="border-t">
                <td class="p-2">{{ $row['name'] }}</td>
                <td class="p-2">{{ $row['count'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    @if($proposal->status === 'submitted')
      <div class="flex gap-3">
        <form action="{{ route('learning.reviews.approve',$proposal) }}" method="POST" onsubmit="return confirm('Approve dan terapkan?')">
          @csrf
          <button class="px-4 py-2 bg-green-600 text-white rounded">Approve & Apply</button>
        </form>
        <form action="{{ route('learning.reviews.reject',$proposal) }}" method="POST">
          @csrf
          <input type="text" name="reason" placeholder="Alasan penolakan" class="border rounded p-2" required />
          <button class="px-4 py-2 bg-red-600 text-white rounded">Reject</button>
        </form>
      </div>
    @elseif($proposal->status === 'approved')
      <div class="flex items-center gap-4 mt-2">
        <span class="text-sm text-green-700">Sudah disetujui.</span>
        <form action="{{ route('learning.reviews.approve',$proposal) }}" method="POST" onsubmit="return confirm('Re-apply akan memperbarui target & rekomendasi. Lanjutkan?')">
          @csrf
          <button class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs">Re-apply</button>
        </form>
      </div>
    @else
      <div class="text-sm text-gray-500">Status: {{ ucfirst($proposal->status) }}</div>
    @endif
  </div>
@endsection
