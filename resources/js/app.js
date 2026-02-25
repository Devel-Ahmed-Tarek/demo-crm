import './bootstrap';

const THEME_KEY = 'theme';

if (typeof window !== 'undefined' && typeof document !== 'undefined') {
    const applyTheme = (theme) => {
        const root = document.documentElement;
        if (theme === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        root.dataset.theme = theme;
        try {
            localStorage.setItem(THEME_KEY, theme);
        } catch (error) {
            // Ignore storage errors (private mode, etc.)
        }
    };

    const initTheme = () => {
        let storedTheme = null;
        try {
            storedTheme = localStorage.getItem(THEME_KEY);
        } catch (error) {
            storedTheme = null;
        }

        const prefersDarkQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
        const prefersDark = prefersDarkQuery?.matches ?? false;
        const theme = storedTheme ?? (prefersDark ? 'dark' : 'light');
        applyTheme(theme);

        document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                applyTheme(nextTheme);
            });
        });

        prefersDarkQuery?.addEventListener('change', (event) => {
            let stored = null;
            try {
                stored = localStorage.getItem(THEME_KEY);
            } catch (error) {
                stored = null;
            }

            if (!stored) {
                applyTheme(event.matches ? 'dark' : 'light');
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }
}
