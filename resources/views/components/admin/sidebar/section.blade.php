@props(['title'])

<div>
    <div style="padding: 0 0.75rem; margin-bottom: 0.5rem; font-size: 0.75rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">
        {{ $title }}
    </div>
    <div style="display: flex; flex-direction: column; gap: 0.125rem;">
        {{ $slot }}
    </div>
</div>