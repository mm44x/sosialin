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
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes toast-out {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes toast-progress {
        from {
            transform: scaleX(1);
        }

        to {
            transform: scaleX(0);
        }
    }

    .toast-anim-in {
        animation: toast-in 350ms cubic-bezier(0.21, 1.02, 0.73, 1) forwards;
    }

    .toast-anim-out {
        animation: toast-out 250ms cubic-bezier(0.06, 0.71, 0.55, 1) forwards;
    }

    .toast-progress {
        animation: toast-progress linear forwards;
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
                'min-w-[300px] max-w-[420px] text-sm rounded-xl shadow-lg backdrop-blur-sm p-3 flex gap-3 items-start relative overflow-hidden';
            const byType = {
                success: 'bg-green-500/95 text-white',
                error: 'bg-red-500/95 text-white',
                info: 'bg-slate-800/95 text-white',
            } [type] || 'bg-slate-800/95 text-white';

            wrap.innerHTML = `
      <div class="${base} ${byType}">
        <div class="shrink-0 pt-0.5">
          ${type === 'success' ? `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>` :
          type === 'error' ? `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>` :
          `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>`}
        </div>
        <div class="grow whitespace-pre-wrap leading-5">${(message ?? '').toString()}</div>
        <button type="button" aria-label="Tutup"
          class="shrink-0 p-1 -m-1 rounded-lg hover:bg-white/20 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
          <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
          </svg>
        </button>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/10">
          <div class="h-full bg-white/25 toast-progress" style="animation-duration: ${timeout}ms; transform-origin: left"></div>
        </div>
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
