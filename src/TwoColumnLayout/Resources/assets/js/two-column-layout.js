(function () {
    const STORAGE_PREFIX = 'stream-hub:two-column-layout:left-width';
    const MIN_WIDTH = 256;
    const MAX_WIDTH = 520;
    const MOBILE_MEDIA = '(max-width: 900px)';

    function clamp(value, min, max) {
        return Math.min(max, Math.max(min, value));
    }

    function initLayout(layout, index) {
        const left = layout.querySelector('[data-role="left"]');
        const divider = layout.querySelector('[data-role="divider"]');

        if (!left || !divider) {
            return;
        }

        const storageKey = `${STORAGE_PREFIX}:${index}`;
        const mediaQuery = window.matchMedia(MOBILE_MEDIA);

        const savedWidth = window.localStorage.getItem(storageKey);
        if (savedWidth && !mediaQuery.matches) {
            left.style.width = `${clamp(Number(savedWidth), MIN_WIDTH, MAX_WIDTH)}px`;
        }

        divider.addEventListener('pointerdown', (event) => {
            if (mediaQuery.matches) {
                return;
            }

            event.preventDefault();
            divider.setPointerCapture(event.pointerId);

            const layoutRect = layout.getBoundingClientRect();

            const onMove = (moveEvent) => {
                const nextWidth = clamp(moveEvent.clientX - layoutRect.left, MIN_WIDTH, MAX_WIDTH);
                left.style.width = `${nextWidth}px`;
                window.localStorage.setItem(storageKey, String(nextWidth));
            };

            const onStop = () => {
                divider.removeEventListener('pointermove', onMove);
                divider.removeEventListener('pointerup', onStop);
                divider.removeEventListener('pointercancel', onStop);
                if (divider.hasPointerCapture(event.pointerId)) {
                    divider.releasePointerCapture(event.pointerId);
                }
            };

            divider.addEventListener('pointermove', onMove);
            divider.addEventListener('pointerup', onStop);
            divider.addEventListener('pointercancel', onStop);
        });

        mediaQuery.addEventListener('change', (changeEvent) => {
            if (changeEvent.matches) {
                left.style.width = '';
                return;
            }

            const width = window.localStorage.getItem(storageKey);
            if (width) {
                left.style.width = `${clamp(Number(width), MIN_WIDTH, MAX_WIDTH)}px`;
            }
        });
    }

    const plugin = {
        name: 'two-column-layout',
        init({ queryAll }) {
            queryAll('[data-stream-hub-two-column-layout]')
                .forEach((layout, index) => initLayout(layout, index));
        },
    };

    if (window.StreamHubApp) {
        window.StreamHubApp.register(plugin);
        return;
    }

    document
        .querySelectorAll('[data-stream-hub-two-column-layout]')
        .forEach((layout, index) => initLayout(layout, index));
})();
