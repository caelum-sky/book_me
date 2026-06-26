// resources/js/bm-modal.js
// Custom modal system that avoids the browser top-layer stacking issues
// with native showModal(). We manage visibility ourselves via CSS classes
// and a single backdrop element, giving us full control over open/close order.

(function () {
    // ── State ─────────────────────────────────────────────────────────────────
    let activeModal = null;
    let backdropEl  = null;

    // ── Backdrop ──────────────────────────────────────────────────────────────
    function getBackdrop() {
        if (!backdropEl) {
            backdropEl = document.createElement('div');
            backdropEl.id = 'bm-backdrop';
            backdropEl.style.cssText = [
                'position:fixed', 'inset:0', 'z-index:9998',
                'background:rgba(0,0,0,0.6)',
                'backdrop-filter:blur(2px)',
                '-webkit-backdrop-filter:blur(2px)',
                'display:none',
            ].join(';');
            backdropEl.addEventListener('click', () => bmCloseModal());
            document.body.appendChild(backdropEl);
        }
        return backdropEl;
    }

    // ── Open ──────────────────────────────────────────────────────────────────
    window.bmOpenModal = function (id) {
        // Close whatever is currently open first, synchronously.
        if (activeModal) {
            _hide(activeModal);
            activeModal = null;
        }

        const el = document.getElementById(id);
        if (!el) return;

        getBackdrop().style.display = 'block';
        _show(el);
        activeModal = el;
        document.body.style.overflow = 'hidden';
    };

    // ── Close ─────────────────────────────────────────────────────────────────
    window.bmCloseModal = function (id) {
        const target = id ? document.getElementById(id) : activeModal;
        if (!target) return;
        _hide(target);
        if (activeModal === target) {
            activeModal = null;
            getBackdrop().style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // ── Internal show/hide ────────────────────────────────────────────────────
    function _show(el) {
        el.style.cssText = [
            'position:fixed',
            'top:50%', 'left:50%',
            'transform:translate(-50%,-50%)',
            'z-index:9999',
            'display:flex',
            'flex-direction:column',
            'max-height:88vh',
            'overflow:hidden',
        ].join(';');
        // Keep any existing width/max-width from the CSS class (.bm-modal, .bm-modal.lg, etc.)
        // by NOT setting those here — they come from dashboard.css.
        el.removeAttribute('hidden');
        // If it's a native <dialog>, also set open so CSS :is(dialog[open]) selectors still work.
        if (el.tagName === 'DIALOG') el.setAttribute('open', '');
    }

    function _hide(el) {
        el.style.cssText = 'display:none;';
        if (el.tagName === 'DIALOG') el.removeAttribute('open');
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        // Make sure no dialog starts visible regardless of bfcache.
        document.querySelectorAll('dialog.bm-modal').forEach((d) => {
            _hide(d);
        });
    });
})();