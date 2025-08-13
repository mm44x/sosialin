@php
    $toasts = [];
    if (session('status')) {
        $toasts[] = ['type' => 'success', 'message' => (string) session('status')];
    }
    if (session('error')) {
        $toasts[] = ['type' => 'error', 'message' => (string) session('error')];
    }
    if ($errors->any()) {
        foreach ($errors->all() as $msg) {
            $toasts[] = ['type' => 'error', 'message' => (string) $msg];
        }
    }
@endphp

<style>
    /* Animasi lembut untuk toast (tanpa Tailwind plugin tambahan) */
    @keyframes toast-in {
        from {
            transform: translateY(-8px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes toast-out {
        from {
            transform: translateY(0);
            opacity: 1;
        }

        to {
            transform: translateY(-8px);
            opacity: 0;
        }
    }

    .toast-anim-in {
        animation: toast-in 220ms cubic-bezier(.22, .9, .31, 1);
    }

    .toast-anim-out {
        animation: toast-out 180ms ease-in forwards;
    }
</style>

{{-- ROOT di pojok kanan atas --}}
<div id="toast-root" class="fixed z-[100] right-4 top-4 flex flex-col gap-2 items-end pointer-events-none"></div>

<script>
    (() => {
        const rootId = 'toast-root';

        function ensureRoot() {
            let el = document.getElementById(rootId);
            if (!el) {
                el = document.createElement('div');
                el.id = rootId;
                el.className = 'fixed z-[100] right-4 top-4 flex flex-col gap-2 items-end pointer-events-none';
                document.body.appendChild(el);
            }
            return el;
        }

        // API publik sederhana:
        // window.toast({ message, type: 'success' | 'error' | 'info', timeout: ms })
        window.toast = function({
            message,
            type = 'success',
            timeout = 4000
        } = {}) {
            const root = ensureRoot();

            const wrap = document.createElement('div');
            wrap.className = 'pointer-events-auto toast-anim-in';
            wrap.setAttribute('role', type === 'error' ? 'alert' : 'status');
            wrap.setAttribute('aria-live', 'polite');

            const base =
                'min-w-[260px] max-w-[420px] text-sm rounded-2xl shadow-lg ring-1 p-3 flex gap-3 items-start';
            const byType = {
                success: 'bg-green-600 text-white ring-green-300',
                error: 'bg-red-600 text-white ring-red-300',
                info: 'bg-slate-800 text-white ring-slate-600',
            } [type] || 'bg-slate-800 text-white ring-slate-600';

            wrap.innerHTML = `
      <div class="${base} ${byType}">
        <div class="shrink-0 pt-0.5">
          ${type === 'success' ? `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>` :
          type === 'error' ? `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M12 5a7 7 0 00-7 7 7 7 0 0014 0 7 7 0 00-7-7z"/>
            </svg>` :
          `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
            </svg>`}
        </div>
        <div class="grow whitespace-pre-wrap leading-5">${(message ?? '').toString()}</div>
        <button type="button" aria-label="Tutup"
          class="shrink-0 p-1 rounded-lg hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
          ✕
        </button>
      </div>
    `;

            const closeBtn = wrap.querySelector('button[aria-label="Tutup"]');
            let timer = null;

            const remove = () => {
                if (!wrap.isConnected) return;
                wrap.classList.remove('toast-anim-in');
                wrap.classList.add('toast-anim-out');
                setTimeout(() => wrap.remove(), 180); // sinkron dengan @keyframes toast-out
            };

            closeBtn?.addEventListener('click', () => {
                clearTimeout(timer);
                remove();
            });
            wrap.addEventListener('mouseenter', () => clearTimeout(timer));
            wrap.addEventListener('mouseleave', () => {
                if (timeout > 0) timer = setTimeout(remove, timeout);
            });

            root.appendChild(wrap);
            if (timeout > 0) timer = setTimeout(remove, timeout);
        };

        // Render awal dari session/errors
        const initial = @json($toasts);
        if (Array.isArray(initial) && initial.length) {
            document.addEventListener('DOMContentLoaded', () => {
                initial.forEach(t => window.toast(t));
            });
        }
    })();
</script>
