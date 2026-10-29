<style>
    .management-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-height: 132px;
        padding: 1.25rem 1.35rem;
        color: var(--text-dark);
        text-decoration: none;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 1.25rem;
        box-shadow: 0 10px 30px rgba(59, 51, 49, 0.04);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .management-card:hover {
        color: var(--text-dark);
        border-color: color-mix(in srgb, var(--primary-color) 45%, white);
        box-shadow: 0 16px 34px rgba(59, 51, 49, .1);
        transform: translateY(-3px);
    }

    .management-card-icon {
        display: inline-grid;
        flex: 0 0 48px;
        width: 48px;
        height: 48px;
        place-items: center;
        color: var(--primary-color);
        background: color-mix(in srgb, var(--primary-color) 12%, white);
        border-radius: 1rem;
        font-size: 1.15rem;
    }

    .management-card-content {
        display: flex;
        min-width: 0;
        flex: 1 1 auto;
        flex-direction: column;
        gap: .3rem;
    }

    .management-card-content strong {
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
    }

    .management-card-content small {
        color: var(--text-muted);
        line-height: 1.5;
    }

    .management-card-arrow {
        color: var(--text-muted);
        transition: transform .2s ease, color .2s ease;
    }

    .management-card:hover .management-card-arrow {
        color: var(--primary-color);
        transform: translateX(3px);
    }
</style>
