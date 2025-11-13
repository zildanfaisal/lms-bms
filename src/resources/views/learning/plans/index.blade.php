@extends('layouts.master')

@section('title', 'Usulan Rencana Belajar')

@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Usulan Rencana Belajar</h1>
@endsection

@section('content')
  <div class="bg-white rounded-xl shadow p-4">
    @if (session('success'))
      <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif
    
    <div class="flex flex-col gap-4 mb-4">
      <div class="flex justify-between items-center">
        <div class="font-semibold">Daftar Usulan</div>
        <a href="{{ route('learning.plans.create') }}" class="px-3 py-2 rounded bg-purple-600 text-white text-sm">Buat Usulan</a>
      </div>
      <form method="GET" class="bg-gray-50 rounded p-3 grid grid-cols-1 md:grid-cols-5 gap-3 text-sm">
        <div>
          <label class="block text-xs text-gray-600">Periode</label>
          <select name="period_id" class="mt-1 w-full border rounded p-1">
            <option value="">Semua</option>
            @foreach($periodOptions as $po)
              <option value="{{ $po->id }}" @selected($activeFilters['period_id']==$po->id)>{{ $po->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-600">Status</label>
          <select name="status" class="mt-1 w-full border rounded p-1">
            <option value="">Semua</option>
            @foreach(['draft','submitted','approved','rejected'] as $st)
              <option value="{{ $st }}" @selected($activeFilters['status']==$st)>{{ ucfirst($st) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-600">Scope Type</label>
          <select name="scope_type" class="mt-1 w-full border rounded p-1">
            <option value="">Semua</option>
            @foreach($allowedScopeTypes as $st)
              <option value="{{ $st }}" @selected($activeFilters['scope_type']==$st)>{{ ucfirst($st) }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex items-end gap-2 md:col-span-2">
          <button class="px-3 py-2 bg-indigo-600 text-white rounded">Filter</button>
          <a href="{{ route('learning.plans.index') }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded">Reset</a>
        </div>
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-2">No</th>
            <th class="text-left p-2">Periode</th>
            <th class="text-left p-2">Scope</th>
            <th class="text-left p-2">Target (menit)</th>
            <th class="text-left p-2">Status</th>
            <th class="text-left p-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($proposals as $i => $p)
            <tr class="border-b">
              <td class="p-2">{{ $proposals->firstItem() + $i }}</td>
              <td class="p-2">{{ $p->period->name ?? '-' }}</td>
              <td class="p-2">
                <div class="flex flex-col">
                  <span class="font-medium">{{ ucfirst($p->scope_type) }}</span>
                  <span class="text-xs text-gray-600">{{ $scopeNames[$p->scope_type.'-'.$p->scope_id] ?? '#'.$p->scope_id }}</span>
                </div>
              </td>
              <td class="p-2">{{ $p->target_minutes ?? '-' }}</td>
              <td class="p-2">
                @php
                  $statusColor = match($p->status) {
                    'draft' => 'bg-gray-200 text-gray-700',
                    'submitted' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-600'
                  };
                @endphp
                <span class="px-2 py-1 rounded text-xs font-medium {{ $statusColor }}">{{ ucfirst($p->status) }}</span>
                @if($p->scope_type==='unit' && $p->only_subordinate_jabatans)
                  <span class="ml-1 px-2 py-1 rounded text-xs bg-indigo-50 text-indigo-700">Bawahan</span>
                @endif
              </td>
              <td class="p-2 space-x-2">
                @if($p->status === 'draft')
                  <a href="{{ route('learning.plans.edit',$p) }}" class="text-purple-600">Edit</a>
                  <form action="{{ route('learning.plans.submit',$p) }}" method="POST" class="inline" data-confirm="Kirim usulan ke HR?">
                    @csrf
                    <button class="text-green-700">Submit</button>
                  </form>
                @else
                  <a href="{{ route('learning.plans.edit',$p) }}" class="text-gray-400 pointer-events-none">Edit</a>
                @endif
                <button type="button" class="text-indigo-600 ml-2 btn-history" data-url="{{ route('learning.plans.history',$p) }}">Riwayat</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- Modal Riwayat -->
    <div id="history-overlay" class="fixed inset-0 bg-black/40 z-40 hidden"></div>
    <div id="history-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">
        <div class="flex items-center justify-between p-4 border-b">
          <h3 class="font-semibold text-gray-800 text-sm">Riwayat Usulan</h3>
          <button id="history-close" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <div class="p-4 text-sm" id="history-content">
          <div class="text-gray-500">Memuat...</div>
        </div>
        <div class="p-4 border-t text-right">
          <button id="history-close-2" class="px-3 py-2 bg-gray-200 rounded">Tutup</button>
        </div>
      </div>
    </div>
    <script>
    (function(){
      const overlay = document.getElementById('history-overlay');
      const modal = document.getElementById('history-modal');
      const content = document.getElementById('history-content');
      function openHistory(){ overlay.classList.remove('hidden'); modal.classList.remove('hidden'); modal.classList.add('flex'); }
      function closeHistory(){ overlay.classList.add('hidden'); modal.classList.add('hidden'); modal.classList.remove('flex'); }
      document.getElementById('history-close')?.addEventListener('click', closeHistory);
      document.getElementById('history-close-2')?.addEventListener('click', closeHistory);
      overlay?.addEventListener('click', closeHistory);
      document.addEventListener('click', async function(e){
        const btn = e.target.closest && e.target.closest('.btn-history');
        if(!btn) return;
        e.preventDefault();
        const url = btn.getAttribute('data-url');
        if(!url) return;
        content.innerHTML = '<div class="text-gray-500">Memuat...</div>';
        openHistory();
        try{
          const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
          if(!resp.ok){ throw new Error('Gagal memuat'); }
          const d = await resp.json();
          const badge = (st)=>{
            const m = {draft:'bg-gray-200 text-gray-700',submitted:'bg-yellow-100 text-yellow-800',approved:'bg-green-100 text-green-800',rejected:'bg-red-100 text-red-800'};
            const cls = m[st] || 'bg-gray-100 text-gray-700';
            return `<span class="px-2 py-1 rounded text-xs font-medium ${cls}">${(st||'').charAt(0).toUpperCase()+ (st||'').slice(1)}</span>`;
          };
          const recRows = (d.recommendations||[]).map((r,i)=>`
            <tr class="border-b">
              <td class="p-2">${i+1}</td>
              <td class="p-2">${r.title||'-'}</td>
              <td class="p-2"><a class="text-indigo-600 hover:underline" href="${r.url||'#'}" target="_blank">${r.url? 'Link' : '-'}</a></td>
              <td class="p-2">${r.target_minutes ?? '-'}</td>
            </tr>
          `).join('');
          const appliedInfo = d.status==='approved' ? `<div class="text-xs text-green-700">Rekomendasi diterapkan: ${d.applied_recommendations} item</div>` : '';
          content.innerHTML = `
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div>
                <div class="text-xs text-gray-500">Periode</div>
                <div class="font-medium">${d.period || '-'}</div>
              </div>
              <div>
                <div class="text-xs text-gray-500">Scope</div>
                <div class="font-medium">${(d.scope_type||'-').toUpperCase()} ${d.scope_name? ' · '+d.scope_name : ''}</div>
              </div>
              <div>
                <div class="text-xs text-gray-500">Status</div>
                <div class="flex items-center gap-2">${badge(d.status)} ${appliedInfo}</div>
              </div>
              <div>
                <div class="text-xs text-gray-500">Approver & Waktu</div>
                <div class="font-medium">${d.approved_by || '-'} ${d.approved_at? ' · '+d.approved_at : ''}</div>
              </div>
            </div>
            ${d.rejected_reason? `<div class="mb-3 p-2 rounded bg-red-50 text-red-700 text-xs">Alasan reject: ${d.rejected_reason}</div>` : ''}
            <div class="mb-2 font-semibold">Rekomendasi</div>
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="text-left p-2">No</th>
                    <th class="text-left p-2">Judul</th>
                    <th class="text-left p-2">URL</th>
                    <th class="text-left p-2">Target (menit)</th>
                  </tr>
                </thead>
                <tbody>${recRows || '<tr><td colspan="4" class="p-3 text-center text-gray-500">Tidak ada item</td></tr>'}</tbody>
              </table>
            </div>
          `;
        }catch(err){
          content.innerHTML = '<div class="text-red-600">Gagal memuat riwayat.</div>';
        }
      });
    })();
    </script>
    <div class="mt-4">{{ $proposals->links() }}</div>
  </div>
@endsection
