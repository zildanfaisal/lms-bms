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
                            <svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path d="M12.75 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM7.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM8.25 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM9.75 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM10.5 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12.75 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM14.25 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 13.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                                <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
                            </svg>
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
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Target Belajar (menit)</div>
                    <div class="text-lg font-semibold text-red-600">{{ $learningTargetMinutes ?? '-' }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md flex items-center gap-4 border border-yellow-50">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 0 3-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 0 0-.673-.05A3 3 0 0 0 15 1.5h-1.5a3 3 0 0 0-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6ZM13.5 3A1.5 1.5 0 0 0 12 4.5h4.5A1.5 1.5 0 0 0 15 3h-1.5Z" clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V9.375Zm9.586 4.594a.75.75 0 0 0-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 0 0-1.06 1.06l1.5 1.5a.75.75 0 0 0 1.116-.062l3-3.75Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Approved Minutes</div>
                    <div class="text-lg font-semibold text-green-600">{{ $learningApprovedMinutes ?? 0 }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-md flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M2.25 13.5a8.25 8.25 0 0 1 8.25-8.25.75.75 0 0 1 .75.75v6.75H18a.75.75 0 0 1 .75.75 8.25 8.25 0 0 1-16.5 0Z" clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M12.75 3a.75.75 0 0 1 .75-.75 8.25 8.25 0 0 1 8.25 8.25.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75V3Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Progress</div>
                    @php
                        $t = $learningTargetMinutes ?? 0;
                        $m = $learningApprovedMinutes ?? 0;
                        $pct = $t ? min(100, ($m / max(1,$t)) * 100) : null;
                    @endphp
                    <div class="text-lg font-semibold text-blue-600">{{ $pct !== null ? number_format($pct,0).'%' : '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Recommendations for the user --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center gap-3">
                    <div class="font-semibold">Rekomendasi untuk Anda</div>
                    <div class="flex items-center gap-1 text-[11px]">
                        <button type="button" class="rec-filter px-2 py-1 rounded border bg-gray-100 text-gray-700" data-filter="all">Semua</button>
                        <button type="button" class="rec-filter px-2 py-1 rounded border bg-gray-100 text-gray-700" data-filter="not-done">Belum</button>
                        <button type="button" class="rec-filter px-2 py-1 rounded border bg-gray-100 text-gray-700" data-filter="done">Selesai</button>
                    </div>
                </div>
            </div>
            <div class="p-4 recommendation-wrapper transition-opacity duration-10" style="opacity:0;">
                @if (!empty($recommendedItems))
                    <ul class="space-y-3">
                        @foreach ($recommendedItems as $idx => $item)
                            <li class="flex items-start gap-3 recommended-item" data-done="{{ !empty($item['done']) ? '1' : '0' }}">
                                <div class="mt-1 w-2 h-2 rounded-full bg-indigo-500"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <div class="text-sm font-medium text-gray-800 truncate flex items-center gap-2">
                                            <span>{{ $item['title'] }}</span>
                                            @if(!empty($item['done']))
                                                <span class="inline-flex items-center text-green-600 text-[10px] gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8.5 12.086l6.793-6.793a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    Selesai
                                                </span>
                                            @elseif(!empty($item['pending']))
                                                <span class="inline-flex items-center text-yellow-600 text-[10px] gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-4.25a.75.75 0 001.5 0v-4.5a.75.75 0 00-1.5 0v4.5zM10 6a.875.875 0 110 1.75A.875.875 0 0110 6z" clip-rule="evenodd"/></svg>
                                                    Menunggu Approve
                                                </span>
                                            @endif
                                        </div>
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
                                        <button
                                            type="button"
                                            class="text-xs text-indigo-600 hover:underline rec-detail-btn"
                                            data-rec-id="{{ $item['id'] ?? '' }}"
                                            data-title="{{ e($item['title'] ?? '') }}"
                                            data-url="{{ e($item['url'] ?? '') }}"
                                            data-platform-id="{{ $item['platform_id'] ?? '' }}"
                                            data-platform-name="{{ e($platformName ?? '') }}"
                                            data-target-minutes="{{ $item['target_minutes'] ?? '' }}"
                                            data-pending="{{ !empty($item['pending']) ? '1' : '0' }}"
                                            data-done="{{ !empty($item['done']) ? '1' : '0' }}"
                                        >Detail</button>
                                        @php($isDone = !empty($item['done']) || !empty($item['pending']))
                                        <a href="{{ !$isDone ? route('learning.logs.create', [
                                            'title' => $item['title'] ?? null,
                                            'platform_id' => $item['platform_id'] ?? null,
                                            'learning_url' => $item['url'] ?? null,
                                            'duration_minutes' => $item['target_minutes'] ?? null,
                                            'recommendation_id' => $item['id'] ?? null,
                                            'period_id' => $selectedPeriodId ?? null,
                                        ]) : '#' }}"
                                           class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 {{ $isDone ? 'pointer-events-none opacity-50' : '' }}"
                                           @if($isDone) aria-disabled="true" tabindex="-1" @endif
                                        >Catat</a>
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
                @if(!empty($allPeriodStats))
                <div class="bg-white rounded-xl shadow p-5">
                        <h2 class="font-semibold text-sm mb-3">Ringkasan Per Periode</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left p-2">Periode</th>
                                        <th class="text-left p-2">Target (menit)</th>
                                        <th class="text-left p-2">Approved (menit)</th>
                                        <th class="text-left p-2">Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allPeriodStats as $ps)
                                    <tr class="border-b {{ ($ps['id'] === ($learningPeriod->id ?? null)) ? 'bg-indigo-50' : '' }}">
                                        <td class="p-2">{{ $ps['name'] }}</td>
                                        <td class="p-2">{{ $ps['target'] ?? '-' }}</td>
                                        <td class="p-2">{{ $ps['approved'] ?? 0 }}</td>
                                        <td class="p-2">{{ $ps['progress_pct'] !== null ? number_format($ps['progress_pct'],0).'%' : '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                </div>
                @endif
    </div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('rec-detail-modal');
    const overlay = document.getElementById('rec-detail-overlay');
    const titleEl = document.getElementById('rec-title');
    const platformEl = document.getElementById('rec-platform');
    const targetEl = document.getElementById('rec-target');
    const linkEl = document.getElementById('rec-link');
    const belajarBtn = document.getElementById('rec-belajar');
    const catatBtn = document.getElementById('rec-catat');

    function openModal(payload){
    const { id, title, url, platformId, platformName, targetMinutes, done, pending } = payload;
        if (titleEl) titleEl.textContent = title || '-';
        if (platformEl) platformEl.textContent = platformName || '-';
        if (targetEl) targetEl.textContent = targetMinutes ? `${targetMinutes} menit` : '-';
        if (linkEl) { linkEl.href = url || '#'; linkEl.textContent = url || '-'; }
        if (belajarBtn) {
            const dest = id ? ("{{ route('learning.recommendations.click', 0) }}".replace('/0', '/' + id)) : (url || '#');
            try { belajarBtn.setAttribute('href', dest); } catch(e) { belajarBtn.href = dest; }
        }
        const currentPeriodId = "{{ $selectedPeriodId ?? '' }}";
        const params = new URLSearchParams({
            title: title || '',
            platform_id: platformId || '',
            learning_url: url || '',
            duration_minutes: targetMinutes || '',
            recommendation_id: id || '',
            period_id: currentPeriodId
        });
        if (catatBtn) {
            if (done === '1') {
                catatBtn.href = '#';
                catatBtn.setAttribute('aria-disabled','true');
                catatBtn.setAttribute('tabindex','-1');
                catatBtn.classList.add('pointer-events-none','opacity-50');
            } else {
                try { catatBtn.setAttribute('href', "{{ route('learning.logs.create') }}" + '?' + params.toString()); } catch(e) { catatBtn.href = "{{ route('learning.logs.create') }}" + '?' + params.toString(); }
                catatBtn.removeAttribute('aria-disabled');
                catatBtn.removeAttribute('tabindex');
                catatBtn.classList.remove('pointer-events-none','opacity-50');
            }
        }
        // Show modal using inline styles to avoid FOUC before CSS loads
        if (overlay) overlay.style.display = 'block';
        if (modal) modal.style.display = 'flex';
    }

    function closeModal(){
        // Hide modal using inline styles so it's always hidden even before CSS loads
        if (overlay) overlay.style.display = 'none';
        if (modal) modal.style.display = 'none';
    }

        // Event delegation: handle clicks on any .rec-detail-btn (works for dynamic items too)
        document.addEventListener('click', function(ev){
            const btn = ev.target.closest && ev.target.closest('.rec-detail-btn');
            if (!btn) return;
            ev.preventDefault();
            openModal({
                id: btn.dataset.recId || '',
                title: btn.dataset.title || '',
                url: btn.dataset.url || '',
                platformId: btn.dataset.platformId || '',
                platformName: btn.dataset.platformName || '',
                targetMinutes: btn.dataset.targetMinutes || '',
                done: btn.dataset.done || '0',
                pending: btn.dataset.pending || '0',
            });
        });
        // Backward compatibility: custom event still supported
        document.addEventListener('open-rec-modal', (e)=> openModal(e.detail || {}));
    document.getElementById('rec-close')?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);
        // Fade-in recommendations to avoid FOUC (hide inline before CSS loads)
        requestAnimationFrame(()=>{
            document.querySelectorAll('.recommendation-wrapper').forEach(el=>{
                try { el.style.opacity = '1'; } catch(e) { /* no-op */ }
            });
        });
    // Filtering controls for recommendations
        const filterButtons = Array.from(document.querySelectorAll('.rec-filter'));
        const items = Array.from(document.querySelectorAll('.recommended-item'));
        function setActive(btn){
            filterButtons.forEach(b=>{
                b.classList.remove('bg-indigo-600','text-white');
                b.classList.add('bg-gray-100','text-gray-700');
            });
            btn.classList.remove('bg-gray-100','text-gray-700');
            btn.classList.add('bg-indigo-600','text-white');
        }
        function applyFilter(mode){
            items.forEach(li=>{
                const done = li.getAttribute('data-done') === '1';
                let show = true;
                if(mode === 'done') show = done;
                else if(mode === 'not-done') show = !done;
                if(show) li.classList.remove('hidden'); else li.classList.add('hidden');
            });
        }
        // Default: show all and mark first button active
        if(filterButtons.length){ setActive(filterButtons[0]); }
        applyFilter('all');
        filterButtons.forEach(btn=>{
            btn.addEventListener('click', ()=>{ setActive(btn); applyFilter(btn.dataset.filter || 'all'); });
        });

});
</script>
<style>
.modal-enter { opacity: 0; }
.modal-enter-active { opacity: 1; transition: opacity .15s ease-in; }
</style>
@endpush

<!-- Modal HTML (rendered directly to ensure presence even if layout doesn't include @stack('modals')) -->
<div id="rec-detail-overlay" class="fixed inset-0 bg-black/40 z-40" style="display:none;"></div>
<div id="rec-detail-modal" class="fixed inset-0 flex items-center justify-center z-50" style="display:none;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-3">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <div class="font-semibold">Detail Rekomendasi</div>
            <button id="rec-close" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="p-5 space-y-3">
            <div>
                <div class="text-xs text-gray-500">Judul</div>
                <div id="rec-title" class="font-medium text-gray-800">-</div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="text-xs text-gray-500">Platform</div>
                    <div id="rec-platform" class="text-gray-700">-</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Target Menit</div>
                    <div id="rec-target" class="text-gray-700">-</div>
                </div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Link</div>
                <a id="rec-link" href="#" target="_blank" class="text-indigo-600 text-sm break-all hover:underline">-</a>
            </div>
        </div>
        <div class="px-5 py-4 border-t flex justify-end gap-2">
            <a id="rec-catat" href="#" class="px-3 py-2 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-700">Catat</a>
            <a id="rec-belajar" href="#" target="_blank" class="px-3 py-2 rounded bg-gray-100 text-gray-800 text-sm hover:bg-gray-200">Belajar</a>
        </div>
    </div>
</div>