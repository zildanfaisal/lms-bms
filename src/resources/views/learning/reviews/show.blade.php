@extends('layouts.master')

@section('title', 'Detail Usulan Rencana')

@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Detail Usulan Rencana</h1>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const overlay = document.getElementById('rev-rec-overlay');
  const modal = document.getElementById('rev-rec-modal');
  const closeBtn = document.getElementById('rev-rec-close');
  const closeBtn2 = document.getElementById('rev-rec-close-2');
  const titleEl = document.getElementById('rev-rec-title');
  const targetEl = document.getElementById('rev-rec-target');
  const linkEl = document.getElementById('rev-rec-link');
  const openBtn = document.getElementById('rev-rec-open');

  function open(payload){
    titleEl.textContent = payload.title || '-';
    targetEl.textContent = payload.targetMinutes ? payload.targetMinutes + ' menit' : '-';
    const u = payload.url || '#';
    linkEl.href = u; linkEl.textContent = u;
    openBtn.href = u;
    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
  }
  function close(){ overlay.classList.add('hidden'); modal.classList.add('hidden'); }

  document.addEventListener('click', function(ev){
    const btn = ev.target.closest && ev.target.closest('.rec-detail-btn');
    if (!btn) return;
    ev.preventDefault();
    open({
      title: btn.dataset.title || '',
      url: btn.dataset.url || '',
      targetMinutes: btn.dataset.targetMinutes || ''
    });
  });
  overlay?.addEventListener('click', close);
  closeBtn?.addEventListener('click', close);
  closeBtn2?.addEventListener('click', close);
});
</script>
@endpush

@section('content')
  <div class="bg-white rounded-xl shadow p-4 space-y-4">
    @if (session('success'))
      <div class="p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
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
      <div class="text-sm text-gray-500 flex items-center justify-between">
        <span>Rekomendasi</span>
        @if($proposal->recommendations->count())
          <span class="text-xs text-gray-400">Klik Detail untuk melihat target menit & tautan sebelum approve.</span>
        @endif
      </div>
      <ul class="mt-1 space-y-2">
        @forelse($proposal->recommendations as $r)
          <li class="p-2 rounded border flex items-start gap-3">
            <div class="flex-1 min-w-0">
              <div class="font-medium text-sm text-gray-800 flex items-center gap-2">
                <span>{{ $r->title }}</span>
                @if($r->target_minutes)
                  <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-indigo-50 text-indigo-600 border border-indigo-200">{{ $r->target_minutes }}m</span>
                @endif
              </div>
              @if($r->url)
                <div class="text-[11px] text-gray-500 truncate">{{ $r->url }}</div>
              @endif
            </div>
            <div class="flex flex-col gap-1">
              <button type="button" class="px-2 py-1 text-[11px] rounded bg-gray-100 hover:bg-gray-200 rec-detail-btn"
                data-title="{{ e($r->title) }}"
                data-url="{{ e($r->url) }}"
                data-target-minutes="{{ $r->target_minutes ?? '' }}"
              >Detail</button>
              @if($r->url)
                <a href="{{ $r->url }}" target="_blank" class="px-2 py-1 text-[11px] rounded bg-indigo-600 text-white hover:bg-indigo-700">Buka</a>
              @endif
            </div>
          </li>
        @empty
          <li class="text-gray-500 text-sm">Tidak ada rekomendasi</li>
        @endforelse
      </ul>
    </div>

    <!-- Modal for recommendation detail (approval view) -->
    <div id="rev-rec-overlay" class="fixed inset-0 bg-black/40 z-40 hidden"></div>
    <div id="rev-rec-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-5 py-3 border-b flex items-center justify-between">
          <div class="font-semibold text-sm">Detail Rekomendasi</div>
          <button id="rev-rec-close" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="p-5 space-y-3">
          <div>
            <div class="text-xs text-gray-500">Judul</div>
            <div id="rev-rec-title" class="text-sm font-medium text-gray-800">-</div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <div class="text-xs text-gray-500">Target Menit</div>
              <div id="rev-rec-target" class="text-sm text-gray-700">-</div>
            </div>
            <div>
              <div class="text-xs text-gray-500">Link</div>
              <a id="rev-rec-link" href="#" target="_blank" class="text-indigo-600 text-xs break-all hover:underline">-</a>
            </div>
          </div>
        </div>
        <div class="px-5 py-3 border-t flex justify-end gap-2">
          <a id="rev-rec-open" href="#" target="_blank" class="px-3 py-1.5 rounded bg-gray-100 text-gray-700 text-xs hover:bg-gray-200">Buka Asli</a>
          <button id="rev-rec-close-2" class="px-3 py-1.5 rounded bg-indigo-600 text-white text-xs hover:bg-indigo-700">Tutup</button>
        </div>
      </div>
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
  <form action="{{ route('learning.reviews.approve',$proposal) }}" method="POST" data-confirm="Setujui dan terapkan rekomendasi?">
          @csrf
          <button class="px-4 py-2 bg-green-600 text-white rounded">Approve & Apply</button>
        </form>
        <form action="{{ route('learning.reviews.reject',$proposal) }}" method="POST" data-confirm="Tolak usulan ini?">
          @csrf
          <input type="text" name="reason" placeholder="Alasan penolakan" class="border rounded p-2" required />
          <button class="px-4 py-2 bg-red-600 text-white rounded">Reject</button>
        </form>
      </div>
    @elseif($proposal->status === 'approved')
      <div class="flex items-center gap-4 mt-2">
        <span class="text-sm text-green-700">Sudah disetujui.</span>
  <form action="{{ route('learning.reviews.approve',$proposal) }}" method="POST" data-confirm="Re-apply akan memperbarui target & rekomendasi. Lanjutkan?">
          @csrf
          <button class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs">Re-apply</button>
        </form>
      </div>
    @else
      <div class="text-sm text-gray-500">Status: {{ ucfirst($proposal->status) }}</div>
    @endif
  </div>
@endsection
