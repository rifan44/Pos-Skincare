<style>
    :root {
        color-scheme: light;
        --bg-cream: #fdfbf7;
        --card-bg: #ffffff;
        --surface-alt: #f7f3ef;
        --text-dark: #3b3331;
        --text-muted: #8a7b78;
        --primary-color: {{ $primaryColor ?? '#c78b7d' }};
        --primary-hover: #b07466;
        --border-color: #f0e9e4;
        --input-bg: #ffffff;
    }

    html[data-theme="dark"] {
        color-scheme: dark;
        --bg-cream: #191716;
        --card-bg: #24201f;
        --surface-alt: #302b29;
        --text-dark: #f2eae7;
        --text-muted: #b7a9a4;
        --primary-hover: #d59b8d;
        --border-color: #443b38;
        --input-bg: #2b2624;
        --bs-border-color: var(--border-color);
        --bs-body-bg: var(--bg-cream);
        --bs-body-color: var(--text-dark);
    }

    body {
        background-color: var(--bg-cream);
        color: var(--text-dark);
    }

    .theme-toggle {
        width: 2.6rem;
        height: 2.6rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 1px solid var(--border-color);
        border-radius: 50%;
        background-color: var(--card-bg);
        color: var(--text-dark);
    }

    .theme-toggle:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .guest-theme-toggle {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1050;
    }

    html[data-theme="dark"] .card,
    html[data-theme="dark"] .login-card,
    html[data-theme="dark"] .dropdown-menu,
    html[data-theme="dark"] .offcanvas,
    html[data-theme="dark"] .modal-content,
    html[data-theme="dark"] .list-group-item,
    html[data-theme="dark"] .accordion-item {
        border-color: var(--border-color);
        background-color: var(--card-bg);
        color: var(--text-dark);
    }

    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .form-select,
    html[data-theme="dark"] .input-group-text,
    html[data-theme="dark"] input:not([type="checkbox"]):not([type="radio"]):not([type="range"]),
    html[data-theme="dark"] textarea,
    html[data-theme="dark"] select {
        border-color: var(--border-color) !important;
        background-color: var(--input-bg) !important;
        color: var(--text-dark) !important;
    }

    html[data-theme="dark"] input[type="file"]::file-selector-button,
    html[data-theme="dark"] input[type="file"]::-webkit-file-upload-button {
        border-color: var(--border-color);
        background-color: var(--surface-alt);
        color: var(--text-dark);
    }

    html[data-theme="dark"] input[type="checkbox"],
    html[data-theme="dark"] input[type="radio"] {
        accent-color: var(--primary-color);
    }

    html[data-theme="dark"] input::placeholder,
    html[data-theme="dark"] textarea::placeholder,
    html[data-theme="dark"] .form-control::placeholder {
        color: var(--text-muted) !important;
    }

    html[data-theme="dark"] h1,
    html[data-theme="dark"] h2,
    html[data-theme="dark"] h3,
    html[data-theme="dark"] h4,
    html[data-theme="dark"] h5,
    html[data-theme="dark"] h6,
    html[data-theme="dark"] .text-dark,
    html[data-theme="dark"] .text-gray-900,
    html[data-theme="dark"] .text-gray-800,
    html[data-theme="dark"] .text-gray-700,
    html[data-theme="dark"] .login-card label {
        color: var(--text-dark) !important;
    }

    html[data-theme="dark"] .text-muted,
    html[data-theme="dark"] .text-gray-600,
    html[data-theme="dark"] .text-gray-500,
    html[data-theme="dark"] .text-gray-400 {
        color: var(--text-muted) !important;
    }

    html[data-theme="dark"] .bg-white,
    html[data-theme="dark"] .bg-light,
    html[data-theme="dark"] .bg-gray-100,
    html[data-theme="dark"] .theme-toggle {
        border-color: var(--border-color) !important;
        background-color: var(--card-bg) !important;
        color: var(--text-dark) !important;
    }

    html[data-theme="dark"] .btn-light,
    html[data-theme="dark"] .btn-white {
        border-color: var(--border-color) !important;
        background-color: var(--surface-alt) !important;
        color: var(--text-dark) !important;
    }

    html[data-theme="dark"] .navbar .dropdown-toggle {
        border-color: var(--border-color) !important;
        background-color: var(--card-bg) !important;
        color: var(--text-dark) !important;
    }

    html[data-theme="dark"] .border,
    html[data-theme="dark"] .border-top,
    html[data-theme="dark"] .border-bottom,
    html[data-theme="dark"] .border-gray-100,
    html[data-theme="dark"] .border-gray-200,
    html[data-theme="dark"] .border-gray-300 {
        border-color: var(--border-color) !important;
    }

    html[data-theme="dark"] .table {
        --bs-table-bg: var(--card-bg);
        --bs-table-color: var(--text-dark);
        --bs-table-border-color: var(--border-color);
    }

    html[data-theme="dark"] .table-light,
    html[data-theme="dark"] .table-light th,
    html[data-theme="dark"] .table-light td {
        background-color: var(--surface-alt) !important;
        color: var(--text-muted) !important;
    }

    html[data-theme="dark"] .dropdown-item { color: var(--text-dark); }
    html[data-theme="dark"] .dropdown-item:hover,
    html[data-theme="dark"] .dropdown-item:focus { background-color: var(--surface-alt); }
    html[data-theme="dark"] .dropdown-divider { border-color: var(--border-color); }
    html[data-theme="dark"] .navbar {
        border-bottom-color: var(--border-color) !important;
        background-color: rgba(25, 23, 22, .96) !important;
    }
    html[data-theme="dark"] .navbar .nav-link { color: var(--text-muted) !important; }
    html[data-theme="dark"] .navbar .nav-link[style*="background-color"] {
        background-color: var(--surface-alt) !important;
        color: var(--primary-color) !important;
    }
    html[data-theme="dark"] .login-card h2 { color: var(--text-dark) !important; }
    html[data-theme="dark"] .login-card .custom-input { background-color: var(--input-bg); }

    html[data-theme="dark"] .pos-intro {
        border-color: var(--border-color);
        background: linear-gradient(110deg, var(--surface-alt), var(--card-bg) 62%);
    }
    html[data-theme="dark"] .pos-intro-tip,
    html[data-theme="dark"] .pos-toolbar,
    html[data-theme="dark"] .catalog-availability,
    html[data-theme="dark"] .cart-controls .input-group-text:last-child {
        border-color: var(--border-color);
        background: var(--surface-alt) !important;
        color: var(--text-dark);
    }
    html[data-theme="dark"] .pos-panel,
    html[data-theme="dark"] .cart-wrapper,
    html[data-theme="dark"] .catalog-card,
    html[data-theme="dark"] .cart-line {
        border-color: var(--border-color) !important;
        background: var(--card-bg) !important;
    }
    html[data-theme="dark"] .catalog-photo {
        background: linear-gradient(145deg, #302927, #282321);
    }
    html[data-theme="dark"] .catalog-add {
        background: var(--surface-alt);
        color: var(--primary-color);
    }
    html[data-theme="dark"] .catalog-add:hover {
        background: var(--primary-color);
        color: #fff;
    }
    html[data-theme="dark"] .cart-count,
    html[data-theme="dark"] .cart-heading-icon,
    html[data-theme="dark"] .cart-empty-icon {
        background: var(--surface-alt);
    }
    html[data-theme="dark"] .cart-total {
        border-color: var(--border-color);
        background: linear-gradient(105deg, var(--surface-alt), var(--card-bg));
    }
    html[data-theme="dark"] .pos-intro-tip small,
    html[data-theme="dark"] .catalog-heading p,
    html[data-theme="dark"] .cart-heading small { color: var(--text-muted); }
    html[data-theme="dark"] .btn-pay:disabled { background: #413b39; color: #aa9e9a; }
    html[data-theme="dark"] .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
</style>
