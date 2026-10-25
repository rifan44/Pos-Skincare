<script>
    (() => {
        const storageKey = 'pos-skincare-theme';
        const savedTheme = localStorage.getItem(storageKey);

        document.documentElement.dataset.theme =
            savedTheme === 'dark' || savedTheme === 'light' ? savedTheme : 'light';

        document.addEventListener('DOMContentLoaded', () => {
            const toggles = document.querySelectorAll('[data-theme-toggle]');

            const syncToggle = toggle => {
                const isDark = document.documentElement.dataset.theme === 'dark';
                const icon = toggle.querySelector('[data-theme-icon]');

                if (icon) {
                    if (icon.tagName === 'I') {
                        icon.classList.toggle('fa-moon', !isDark);
                        icon.classList.toggle('fa-sun', isDark);
                    } else {
                        icon.textContent = isDark ? '☀' : '☾';
                    }
                }

                const label = isDark ? 'Gunakan tema terang' : 'Gunakan tema gelap';
                toggle.setAttribute('aria-label', label);
                toggle.setAttribute('title', label);
                toggle.setAttribute('aria-pressed', String(isDark));
            };

            toggles.forEach(toggle => {
                syncToggle(toggle);
                toggle.addEventListener('click', () => {
                    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                    document.documentElement.dataset.theme = nextTheme;
                    localStorage.setItem(storageKey, nextTheme);
                    toggles.forEach(syncToggle);
                });
            });
        });
    })();
</script>
