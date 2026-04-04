<style>
    :root {
        --admin-bg: #eef2ff;
        --admin-bg-accent: radial-gradient(circle at top left, rgba(99, 102, 241, 0.18), transparent 28%),
            radial-gradient(circle at top right, rgba(14, 165, 233, 0.12), transparent 24%),
            linear-gradient(180deg, #f8fafc 0%, #eef2ff 48%, #f8fafc 100%);
        --admin-surface: rgba(255, 255, 255, 0.84);
        --admin-surface-strong: #ffffff;
        --admin-surface-soft: #f8fafc;
        --admin-border: rgba(148, 163, 184, 0.24);
        --admin-border-strong: rgba(99, 102, 241, 0.18);
        --admin-text: #0f172a;
        --admin-text-muted: #64748b;
        --admin-text-soft: #94a3b8;
        --admin-primary: #4f46e5;
        --admin-primary-strong: #7c3aed;
        --admin-primary-soft: rgba(79, 70, 229, 0.12);
        --admin-success: #10b981;
        --admin-warning: #f59e0b;
        --admin-danger: #ef4444;
        --admin-shadow: 0 22px 60px -26px rgba(15, 23, 42, 0.24);
        --admin-shadow-soft: 0 16px 34px -24px rgba(15, 23, 42, 0.2);
        --admin-navbar: rgba(15, 23, 42, 0.82);
        --admin-navbar-border: rgba(255, 255, 255, 0.08);
        --admin-navbar-text: #f8fafc;
        --admin-sidebar: rgba(255, 255, 255, 0.76);
        --admin-sidebar-border: rgba(148, 163, 184, 0.2);
        --admin-input: rgba(248, 250, 252, 0.95);
        --admin-chip: rgba(79, 70, 229, 0.1);
    }

    .dark {
        --admin-bg: #020617;
        --admin-bg-accent: radial-gradient(circle at top left, rgba(99, 102, 241, 0.22), transparent 26%),
            radial-gradient(circle at top right, rgba(168, 85, 247, 0.16), transparent 24%),
            linear-gradient(180deg, #020617 0%, #0f172a 52%, #020617 100%);
        --admin-surface: rgba(15, 23, 42, 0.78);
        --admin-surface-strong: #111827;
        --admin-surface-soft: rgba(15, 23, 42, 0.92);
        --admin-border: rgba(148, 163, 184, 0.16);
        --admin-border-strong: rgba(129, 140, 248, 0.34);
        --admin-text: #f8fafc;
        --admin-text-muted: #cbd5e1;
        --admin-text-soft: #94a3b8;
        --admin-primary: #818cf8;
        --admin-primary-strong: #a855f7;
        --admin-primary-soft: rgba(99, 102, 241, 0.18);
        --admin-shadow: 0 28px 80px -28px rgba(2, 6, 23, 0.72);
        --admin-shadow-soft: 0 18px 36px -24px rgba(2, 6, 23, 0.8);
        --admin-navbar: rgba(2, 6, 23, 0.88);
        --admin-navbar-border: rgba(148, 163, 184, 0.12);
        --admin-navbar-text: #f8fafc;
        --admin-sidebar: rgba(2, 6, 23, 0.82);
        --admin-sidebar-border: rgba(148, 163, 184, 0.14);
        --admin-input: rgba(15, 23, 42, 0.94);
        --admin-chip: rgba(129, 140, 248, 0.16);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Figtree', sans-serif;
        background: var(--admin-bg-accent);
        color: var(--admin-text);
        transition: background-color 0.25s ease, color 0.25s ease;
    }

    .admin-shell {
        position: relative;
        min-height: 100vh;
        background: transparent;
    }

    .admin-shell::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at 15% 20%, rgba(99, 102, 241, 0.12), transparent 24%),
            radial-gradient(circle at 85% 10%, rgba(236, 72, 153, 0.1), transparent 22%);
        opacity: 0.8;
        z-index: 0;
    }

    .admin-topbar {
        position: fixed;
        top: 0.9rem;
        left: 1rem;
        right: 1rem;
        z-index: 50;
        height: 72px;
        border: 1px solid var(--admin-navbar-border);
        background: var(--admin-navbar);
        color: var(--admin-navbar-text);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border-radius: 1.5rem;
        box-shadow: var(--admin-shadow);
    }

    .admin-topbar__inner {
        height: 100%;
        padding: 0 1.1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .admin-sidebar {
        position: fixed;
        top: 5.8rem;
        left: 1rem;
        z-index: 45;
        width: 18rem;
        height: calc(100vh - 6.8rem);
        background: var(--admin-sidebar);
        border: 1px solid var(--admin-sidebar-border);
        border-radius: 1.75rem;
        box-shadow: var(--admin-shadow);
        overflow-y: auto;
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
    }

    .admin-sidebar-shell {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-height: 100%;
        padding: 1.1rem;
    }

    .admin-sidebar-brand {
        display: flex;
        gap: 0.9rem;
        padding: 1rem;
        border-radius: 1.35rem;
        border: 1px solid var(--admin-border);
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.12), rgba(14, 165, 233, 0.08), transparent);
        box-shadow: var(--admin-shadow-soft);
    }

    .admin-sidebar-brand__icon {
        width: 2.65rem;
        height: 2.65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
        background: var(--admin-primary-soft);
        color: var(--admin-primary);
        flex-shrink: 0;
    }

    .admin-sidebar-eyebrow {
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--admin-text-soft);
    }

    .admin-sidebar-title {
        margin-top: 0.2rem;
        font-size: 1rem;
        font-weight: 800;
        color: var(--admin-text);
    }

    .admin-sidebar-copy {
        margin-top: 0.3rem;
        font-size: 0.82rem;
        line-height: 1.5;
        color: var(--admin-text-muted);
    }

    .admin-sidebar-shortcuts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.65rem;
    }

    .admin-sidebar-shortcut {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 2.8rem;
        border-radius: 1rem;
        border: 1px solid var(--admin-border);
        background: var(--admin-surface-soft);
        color: var(--admin-text);
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 700;
        transition: transform 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
    }

    .admin-sidebar-shortcut:hover {
        transform: translateY(-1px);
        border-color: var(--admin-border-strong);
        background: var(--admin-primary-soft);
    }

    .admin-sidebar-insights {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.6rem;
    }

    .admin-sidebar-insight {
        padding: 0.85rem 0.8rem;
        border-radius: 1rem;
        border: 1px solid var(--admin-border);
        background: var(--admin-surface-soft);
        text-align: center;
    }

    .admin-sidebar-insight__label {
        display: block;
        font-size: 0.64rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--admin-text-soft);
    }

    .admin-sidebar-insight__value {
        display: block;
        margin-top: 0.45rem;
        font-size: 1.1rem;
        font-weight: 900;
        color: var(--admin-text);
    }

    .admin-sidebar-groups {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }

    .admin-nav-group {
        padding: 0.45rem;
        border-radius: 1.25rem;
        border: 1px solid var(--admin-border);
        background: rgba(255, 255, 255, 0.08);
    }

    .admin-nav-group__toggle {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.8rem;
        padding: 0.55rem 0.55rem 0.75rem;
        border: none;
        background: transparent;
        color: var(--admin-text);
        cursor: pointer;
    }

    .admin-nav-group__left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
        text-align: left;
    }

    .admin-nav-group__icon {
        width: 2.05rem;
        height: 2.05rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.85rem;
        flex-shrink: 0;
    }

    .admin-nav-group__icon--violet { background: rgba(99, 102, 241, 0.14); color: #6366f1; }
    .admin-nav-group__icon--emerald { background: rgba(16, 185, 129, 0.14); color: #10b981; }
    .admin-nav-group__icon--amber { background: rgba(245, 158, 11, 0.14); color: #f59e0b; }
    .admin-nav-group__icon--pink { background: rgba(236, 72, 153, 0.14); color: #ec4899; }
    .admin-nav-group__icon--indigo { background: rgba(79, 70, 229, 0.14); color: #4f46e5; }

    .admin-nav-group__title {
        display: block;
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--admin-text);
    }

    .admin-nav-group__hint {
        display: block;
        margin-top: 0.16rem;
        font-size: 0.74rem;
        color: var(--admin-text-soft);
    }

    .admin-nav-links {
        display: flex;
        flex-direction: column;
        gap: 0.32rem;
    }

    .admin-main {
        position: relative;
        z-index: 1;
        min-height: 100vh;
        padding-top: 6.8rem;
        transition: padding-left 0.3s ease;
    }

    .admin-content {
        padding: 1.1rem;
    }

    .admin-page-wrap {
        border: 1px solid var(--admin-border);
        background: var(--admin-surface);
        border-radius: 2rem;
        box-shadow: var(--admin-shadow-soft);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        padding: 0.95rem;
    }

    .admin-header-card {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.12), rgba(14, 165, 233, 0.06), transparent);
        border: 1px solid var(--admin-border);
        border-radius: 1.6rem;
        box-shadow: var(--admin-shadow-soft);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }

    .admin-surface {
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }

    .admin-search {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 0.75rem 1rem;
        color: var(--admin-navbar-text);
        transition: all 0.2s ease;
    }

    .admin-search--compact {
        max-width: 29rem;
        margin-inline: auto;
    }

    .admin-search:focus-within {
        border-color: rgba(255, 255, 255, 0.18);
        background: rgba(255, 255, 255, 0.12);
    }

    .admin-search input {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        color: var(--admin-navbar-text);
        font-size: 0.95rem;
    }

    .admin-search input::placeholder {
        color: rgba(226, 232, 240, 0.66);
    }

    .admin-tool-button {
        width: 2.8rem;
        height: 2.8rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.08);
        color: var(--admin-navbar-text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .admin-tool-button:hover {
        transform: translateY(-1px);
        background: rgba(255, 255, 255, 0.14);
    }

    .admin-user-button {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.35rem 0.45rem;
        padding-right: 0.85rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.08);
        color: var(--admin-navbar-text);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .admin-user-button:hover {
        background: rgba(255, 255, 255, 0.14);
    }

    .admin-user-avatar {
        width: 2.3rem;
        height: 2.3rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        font-weight: 700;
        color: white;
        background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-strong));
        box-shadow: 0 16px 30px -16px rgba(99, 102, 241, 0.6);
        text-transform: uppercase;
    }

    .admin-dropdown {
        position: absolute;
        right: 0;
        top: 3.4rem;
        width: 15rem;
        border-radius: 1.25rem;
        border: 1px solid var(--admin-border);
        background: var(--admin-surface-strong);
        box-shadow: var(--admin-shadow);
        padding: 0.55rem 0;
        z-index: 100;
    }

    .admin-notification-panel {
        position: absolute;
        right: 0;
        top: 3.4rem;
        width: min(24rem, calc(100vw - 2rem));
        border-radius: 1.35rem;
        border: 1px solid var(--admin-border);
        background: var(--admin-surface-strong);
        box-shadow: var(--admin-shadow);
        padding: 0.9rem;
        z-index: 100;
    }

    .admin-sidebar-toggle-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        padding: 0.9rem 1rem;
        border-radius: 1.2rem;
        border: 1px solid var(--admin-border);
        background: var(--admin-surface-soft);
        box-shadow: var(--admin-shadow-soft);
    }

    .admin-sidebar-mini-toggle {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border: none;
        border-radius: 999px;
        padding: 0.45rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        color: var(--admin-text);
        background: var(--admin-primary-soft);
    }

    .admin-dropdown-link,
    .admin-dropdown-button {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 0.92rem;
        color: var(--admin-text);
        text-decoration: none;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .admin-dropdown-link:hover,
    .admin-dropdown-button:hover {
        background: var(--admin-primary-soft);
        color: var(--admin-primary);
    }

    .admin-section-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.5rem 0;
        color: var(--admin-text);
        background: transparent;
        border: none;
        font-size: 0.94rem;
        font-weight: 700;
        cursor: pointer;
    }

    .admin-section-hint {
        font-size: 0.7rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--admin-text-soft);
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.78rem 0.95rem;
        font-size: 0.92rem;
        font-weight: 600;
        border-radius: 1rem;
        transition: all 0.2s ease;
        position: relative;
        text-decoration: none;
        color: var(--admin-text-muted);
    }

    .nav-link:hover {
        background: var(--admin-primary-soft);
        color: var(--admin-text);
        transform: translateX(2px);
    }

    .nav-link.active {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.14), rgba(124, 58, 237, 0.08));
        color: var(--admin-primary);
        border: 1px solid var(--admin-border-strong);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.04);
    }

    .nav-icon {
        width: 1.15rem;
        height: 1.15rem;
        stroke-width: 1.7;
        flex-shrink: 0;
    }

    .nav-link.active .nav-icon {
        color: var(--admin-primary);
    }

    .nav-link__body {
        display: flex;
        flex-direction: column;
        gap: 0.18rem;
        min-width: 0;
        flex: 1;
    }

    .nav-link__label {
        font-size: 0.9rem;
        font-weight: 700;
        color: inherit;
    }

    .nav-link__description {
        font-size: 0.72rem;
        line-height: 1.35;
        color: var(--admin-text-soft);
    }

    .nav-link__badge {
        margin-left: auto;
        padding: 0.18rem 0.55rem;
        font-size: 0.72rem;
        background: var(--admin-primary-soft);
        color: var(--admin-primary);
        border-radius: 9999px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .admin-sidebar-footer {
        margin-top: auto;
        padding: 1rem;
        border-radius: 1.25rem;
        border: 1px solid var(--admin-border);
        background: var(--admin-surface-soft);
    }

    .admin-sidebar-footer__link {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        color: var(--admin-primary);
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 800;
    }

    .admin-sidebar-footer__copy {
        margin-top: 0.5rem;
        font-size: 0.76rem;
        line-height: 1.5;
        color: var(--admin-text-muted);
    }

    .sidebar-transition {
        transition-property: transform;
        transition-duration: 300ms;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-hidden {
        transform: translateX(-120%);
    }

    .sidebar-visible {
        transform: translateX(0);
    }

    .main-content {
        transition-property: padding-left;
        transition-duration: 300ms;
    }

    .scrollbar-custom::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-custom::-webkit-scrollbar-track {
        background: transparent;
    }

    .scrollbar-custom::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.32);
        border-radius: 20px;
    }

    .logo-text {
        font-weight: 700;
        color: var(--admin-navbar-text);
        font-size: 1.02rem;
        line-height: 1.2;
    }

    .logo-subtext {
        display: block;
        margin-top: 0.15rem;
        font-size: 0.7rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: rgba(226, 232, 240, 0.72);
    }

    .mobile-overlay {
        position: fixed;
        inset: 0;
        z-index: 40;
        background-color: rgba(2, 6, 23, 0.45);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        display: none;
    }

    .mobile-overlay.active {
        display: block;
    }

    .sidebar-toggle {
        padding: 0.7rem;
        border-radius: 999px;
        color: var(--admin-navbar-text);
        cursor: pointer;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.2s ease;
    }

    .sidebar-toggle:hover {
        background: rgba(255, 255, 255, 0.14);
    }

    .admin-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        padding: 0.42rem 0.75rem;
        background: var(--admin-chip);
        color: var(--admin-text-muted);
        font-size: 0.78rem;
        font-weight: 600;
    }

    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--admin-shadow-soft);
    }

    .admin-page-wrap > .space-y-6 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 1rem !important;
    }

    .admin-page-wrap > .space-y-5 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 0.85rem !important;
    }

    .admin-page-wrap [class*="rounded-[1.75rem]"][class*="p-6"] {
        padding: 1rem !important;
    }

    .admin-page-wrap [class*="rounded-[1.5rem]"][class*="p-5"] {
        padding: 0.9rem !important;
    }

    .admin-page-wrap table th,
    .admin-page-wrap table td {
        padding-top: 0.8rem !important;
        padding-bottom: 0.8rem !important;
    }

    .admin-page-wrap .max-h-\[90vh\],
    .admin-page-wrap .max-h-\[75vh\] {
        max-height: 82vh !important;
    }

    .admin-theme-dark .bg-white,
    .dark .bg-white {
        background-color: rgba(15, 23, 42, 0.92) !important;
    }

    .admin-theme-dark .bg-slate-50,
    .dark .bg-slate-50,
    .admin-theme-dark .bg-gray-50,
    .dark .bg-gray-50 {
        background-color: rgba(15, 23, 42, 0.76) !important;
    }

    .admin-theme-dark .bg-slate-900,
    .dark .bg-slate-900 {
        background-color: rgba(2, 6, 23, 0.95) !important;
    }

    .admin-theme-dark .text-slate-900,
    .dark .text-slate-900,
    .admin-theme-dark .text-gray-900,
    .dark .text-gray-900 {
        color: #f8fafc !important;
    }

    .admin-theme-dark .text-slate-700,
    .dark .text-slate-700,
    .admin-theme-dark .text-slate-600,
    .dark .text-slate-600,
    .admin-theme-dark .text-slate-500,
    .dark .text-slate-500,
    .admin-theme-dark .text-gray-700,
    .dark .text-gray-700,
    .admin-theme-dark .text-gray-600,
    .dark .text-gray-600,
    .admin-theme-dark .text-gray-500,
    .dark .text-gray-500 {
        color: #cbd5e1 !important;
    }

    .admin-theme-dark .border-slate-200,
    .dark .border-slate-200,
    .admin-theme-dark .border-gray-200,
    .dark .border-gray-200 {
        border-color: rgba(148, 163, 184, 0.14) !important;
    }

    .admin-theme-dark .shadow-sm,
    .dark .shadow-sm,
    .admin-theme-dark .shadow-xl,
    .dark .shadow-xl {
        box-shadow: var(--admin-shadow-soft) !important;
    }

    .admin-theme-dark input,
    .dark input,
    .admin-theme-dark select,
    .dark select,
    .admin-theme-dark textarea,
    .dark textarea {
        background-color: var(--admin-input);
        color: var(--admin-text);
        border-color: var(--admin-border);
    }

    .admin-theme-dark input::placeholder,
    .dark input::placeholder,
    .admin-theme-dark textarea::placeholder,
    .dark textarea::placeholder {
        color: var(--admin-text-soft);
    }

    @media (min-width: 1024px) {
        .mobile-only {
            display: none !important;
        }

        .desktop-only {
            display: block !important;
        }

        .main-content-with-sidebar {
            padding-left: 19.5rem;
        }

        .main-content-full {
            padding-left: 0;
        }

        .desktop-sidebar-toggle {
            display: flex !important;
        }
    }

    @media (max-width: 1023px) {
        .desktop-only {
            display: none !important;
        }

        .mobile-only {
            display: block !important;
        }

        .desktop-sidebar-toggle {
            display: none !important;
        }

        .admin-topbar {
            top: 0;
            left: 0;
            right: 0;
            border-radius: 0;
            height: 68px;
            border-left: none;
            border-right: none;
        }

        .admin-sidebar {
            top: 68px;
            left: 0.75rem;
            width: min(18rem, calc(100vw - 1.5rem));
            height: calc(100vh - 78px);
        }

        .admin-sidebar-shortcuts,
        .admin-sidebar-insights {
            grid-template-columns: 1fr;
        }

        .admin-main {
            padding-top: 5.4rem;
        }

        .admin-content {
            padding: 0.8rem;
        }

        .sidebar-visible {
            transform: translateX(0) !important;
        }

        .sidebar-hidden {
            transform: translateX(-120%) !important;
        }

        .mobile-overlay.active {
            display: block;
        }

        .admin-page-wrap {
            padding: 0.9rem;
            border-radius: 1.5rem;
        }
    }

    [x-cloak] {
        display: none !important;
    }
</style>
