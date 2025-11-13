<script>
// Instant search helpers: debounce, escape, render buttons and attach row clicks
(function(){
    window.IS = window.IS || {};
    const ns = window.IS;

    // Token as string and as ready-to-insert hidden input markup
    ns._csrf = '{{ csrf_token() }}';
    ns._csrfInput = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';

    ns.debounce = function(fn, wait=300){ let t; return function(){ const args=arguments; clearTimeout(t); t=setTimeout(()=>fn.apply(this,args), wait); }; };

    ns.escape = function (str){ if(str == null) return ''; return String(str).replace(/[&<>\"]+/g, function(s){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]); }); };

    const _colorMap = {
        purple: 'text-purple-600',
        red: 'text-red-600',
        green: 'text-green-600',
        gray: 'text-gray-700',
        muted: 'text-gray-500'
    };

    function _clsFrom(input){ return _colorMap[input] || input; }

    ns.renderEdit = function(href, colorOrClass='purple'){
        const cls = _clsFrom(colorOrClass);
        // pencil icon (same as component)
        return `
            <a href="${href}" class="${cls}" onclick="event.stopPropagation();">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                </svg>
            </a>
        `;
    };

    ns.renderDelete = function(action, confirmMsg='Hapus data ini?', colorOrClass='red', method='DELETE', precheckUrl=null){
        const cls = _clsFrom(colorOrClass);
        // use data-confirm attribute to avoid nested quoting issues and render trash icon like component
        return `
            <form action="${action}" method="POST" data-confirm="${confirmMsg}" ${precheckUrl ? `data-precheck="${precheckUrl}"` : ''} class="inline" onsubmit="event.stopPropagation();">
                <input type="hidden" name="_token" value="${ns._csrf}">
                <input type="hidden" name="_method" value="${method}">
                <button type="submit" class="${cls}" aria-label="delete">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>
        `;
    };

    ns.attachRowClicks = function(){
        document.addEventListener('click', function(e){
            const tr = e.target.closest('tr[data-href]');
            if(tr && !(e.target.closest('a') || e.target.closest('button') || e.target.closest('form'))){ window.location = tr.getAttribute('data-href'); }
        });
    };

})();
</script>
