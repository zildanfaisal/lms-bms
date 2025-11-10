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
              <th class="text-left p-2">No</th>
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
              <td class="p-2">
                <div class="flex items-center gap-2">
                  <button type="button"
                          class="text-blue-600 underline text-xs detail-btn"
                          data-log-id="{{ $log->id }}"
                          data-title="{{ $log->title }}"
                          data-platform="{{ $log->platform->name ?? '-' }}"
                          data-duration="{{ $log->duration_minutes }}"
                          data-status="{{ $log->status }}"
                          data-started="{{ optional($log->started_at)->toDateString() }}"
                          data-ended="{{ optional($log->ended_at)->toDateString() }}"
                          data-learning-url="{{ $log->learning_url ?? '' }}"
                          data-evidence-url="{{ $log->evidence_url ?? '' }}"
                          data-description="{{ $log->description ? e($log->description) : '' }}"
                          data-rec-id="{{ $log->recommendation_id ?? '' }}"
                          data-rec-title="{{ optional($log->recommendation)->title ?? '' }}"
                          data-rec-target="{{ optional($log->recommendation)->target_minutes ?? '' }}"
                          data-rec-url="{{ optional($log->recommendation)->url ?? '' }}"
                          data-can-approve="{{ auth()->user()->can('approve learning log') ? '1' : '0' }}"
                  >Detail</button>
                  <span>{{ $log->title }}</span>
                  @if($log->recommendation_id)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-700 border border-green-200" title="Linked to recommendation">
                      ✔ Rekomendasi
                    </span>
                  @endif
                </div>
              </td>
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
    {{-- Detail Modal (hidden by default) --}}
    <div id="logDetailOverlay" style="display:none" class="fixed inset-0 bg-black/40 z-40"></div>
    <div id="logDetailModal" style="display:none" class="fixed inset-x-0 top-24 mx-auto max-w-xl z-50 bg-white shadow-lg rounded-lg border border-gray-200">
      <div class="flex justify-between items-center px-4 py-2 border-b">
        <h2 class="font-semibold text-gray-800 text-sm">Detail Pembelajaran</h2>
        <button type="button" id="closeLogDetail" class="text-gray-500 hover:text-gray-700">✕</button>
      </div>
      <div class="p-4 text-xs" id="logDetailBody">
        <p class="text-gray-500">Memuat...</p>
      </div>
      <div class="px-4 py-2 border-t flex justify-end gap-2">
        <button type="button" id="approveFromModal" class="hidden bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded">Approve</button>
        <button type="button" id="rejectFromModal" class="hidden bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded">Reject</button>
      </div>
    </div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const overlay = document.getElementById('logDetailOverlay');
  const modal = document.getElementById('logDetailModal');
  const body = document.getElementById('logDetailBody');
  const closeBtn = document.getElementById('closeLogDetail');
  const approveBtn = document.getElementById('approveFromModal');
  const rejectBtn = document.getElementById('rejectFromModal');

  function openModal(){ overlay.style.display='block'; modal.style.display='block'; }
  function closeModal(){ overlay.style.display='none'; modal.style.display='none'; body.innerHTML=''; approveBtn.classList.add('hidden'); rejectBtn.classList.add('hidden'); }
  closeBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', closeModal);

  document.body.addEventListener('click', function(e){
    const btn = e.target.closest('.detail-btn');
    if(!btn) return;
    const logId = btn.dataset.logId;
    if(!logId) return;
    const title = btn.dataset.title || '-';
    const platform = btn.dataset.platform || '-';
    const duration = btn.dataset.duration || '-';
    const status = btn.dataset.status || '-';
    const started = btn.dataset.started || '-';
    const ended = btn.dataset.ended || '-';
    const learningUrl = btn.dataset.learningUrl || '';
    const evidenceUrl = btn.dataset.evidenceUrl || '';
    const description = btn.dataset.description || '';
    const recId = btn.dataset.recId || '';
    const recTitle = btn.dataset.recTitle || '';
    const recTarget = btn.dataset.recTarget || '';
    const recUrl = btn.dataset.recUrl || '';
    const canApprove = btn.dataset.canApprove === '1';

    let html = '';
    html += '<dl class="grid grid-cols-1 md:grid-cols-2 gap-3">';
    html += '<div><dt class="font-medium">Employee</dt><dd>' + (btn.closest('tr').querySelector('td:nth-child(2)')?.textContent.trim() || '-') + '</dd></div>';
    html += '<div><dt class="font-medium">Jabatan</dt><dd>' + (btn.closest('tr').querySelector('td:nth-child(3)')?.textContent.trim() || '-') + '</dd></div>';
    html += '<div><dt class="font-medium">Platform</dt><dd>' + platform + '</dd></div>';
    html += '<div><dt class="font-medium">Title</dt><dd>' + title + '</dd></div>';
    html += '<div><dt class="font-medium">Duration</dt><dd>' + duration + ' mins</dd></div>';
    html += '<div><dt class="font-medium">Status</dt><dd class="capitalize">' + status + '</dd></div>';
    html += '<div><dt class="font-medium">Started</dt><dd>' + started + '</dd></div>';
    html += '<div><dt class="font-medium">Ended</dt><dd>' + ended + '</dd></div>';
    html += '<div class="md:col-span-2"><dt class="font-medium">Description</dt><dd>' + (description || '-') + '</dd></div>';
    html += '<div><dt class="font-medium">Learning URL</dt><dd>' + (learningUrl ? '<a class="text-blue-600" href="' + learningUrl + '" target="_blank">Buka</a>' : '-') + '</dd></div>';
    html += '<div><dt class="font-medium">Evidence URL</dt><dd>' + (evidenceUrl ? '<a class="text-blue-600" href="' + evidenceUrl + '" target="_blank">Buka</a>' : '-') + '</dd></div>';
    if(recId){
      html += '<div class="md:col-span-2 mt-2 p-2 rounded border bg-green-50 border-green-200">'
           + '<div class="flex items-center gap-2 text-green-800 text-xs font-semibold">✔ Terkait Rekomendasi</div>'
           + '<div class="mt-1 grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px]">'
           + '<div><span class="font-medium">Judul Rekomendasi:</span> ' + (recTitle || '-') + '</div>'
           + '<div><span class="font-medium">Target Menit:</span> ' + (recTarget || '-') + '</div>'
           + '<div class="md:col-span-2"><span class="font-medium">Link:</span> ' + (recUrl ? '<a class="text-blue-600" href="' + recUrl + '" target="_blank">Buka</a>' : '-') + '</div>'
           + '</div>'
           + '</div>';
    }
    html += '</dl>';
    body.innerHTML = html;
    // Wire actions
    if(canApprove){
      approveBtn.onclick = function(){
        const form = document.createElement('form');
        form.method='POST';
        form.action='/learning/logs/' + logId + '/approve';
        form.innerHTML='@csrf';
        document.body.appendChild(form); form.submit();
      };
      rejectBtn.onclick = function(){
        const form = document.createElement('form');
        form.method='POST';
        form.action='/learning/logs/' + logId + '/reject';
        form.innerHTML='@csrf<input type="hidden" name="reason" value="Insufficient detail" />';
        document.body.appendChild(form); form.submit();
      };
      approveBtn.classList.remove('hidden');
      rejectBtn.classList.remove('hidden');
    } else {
      approveBtn.classList.add('hidden');
      rejectBtn.classList.add('hidden');
    }
    openModal();
  });
});
</script>
@endpush
@endsection
