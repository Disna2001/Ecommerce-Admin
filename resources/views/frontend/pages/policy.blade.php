@extends('layouts.shop')
@section('title', $title)
@section('content')
@php($supportEmail = \App\Models\SiteSetting::get('support_email', \App\Models\SiteSetting::get('support_notification_email', '')))
@php($supportPhone = \App\Models\SiteSetting::get('support_phone', ''))
@php
    $panel = "premium-card !p-8 !rounded-[2.5rem]";
    $muted = "text-[10px] font-black uppercase tracking-[0.2em] text-slate-400";
@endphp

<div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
    <header class="mb-12">
        <nav class="mb-6 flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <a wire:navigate href="/" class="hover:text-[var(--primary)] transition-colors">Matrix</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-slate-900 dark:text-white">Legal Registry</span>
        </nav>
        <p class="text-[var(--primary)] text-[10px] font-black uppercase tracking-[0.3em] mb-4">{{ $eyebrow }}</p>
        <h1 class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $title }}</h1>
        <p class="mt-4 max-w-3xl text-sm font-bold text-slate-400 leading-relaxed">{{ $intro }}</p>
        <div class="mt-8 flex flex-wrap gap-4">
            <a wire:navigate href="{{ route('help-center') }}" class="h-14 px-10 flex items-center justify-center rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl hover:scale-105 transition-all">Support Hub</a>
            <a wire:navigate href="{{ route('track-order') }}" class="h-14 px-10 flex items-center justify-center rounded-full border border-slate-100 bg-white text-[10px] font-black uppercase tracking-widest text-slate-500 shadow-sm transition-all hover:bg-slate-50">Sync Protocol</a>
        </div>
    </header>

    <div class="grid gap-12 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-12">
            @foreach($sections as $section)
                <section class="{{ $panel }}">
                    <p class="{{ $muted }} mb-8">{{ $section['title'] }}</p>
                    <div class="space-y-6 text-[11px] font-bold text-slate-500 leading-loose">
                        @foreach($section['body'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <aside class="space-y-12">
            <section class="{{ $panel }}">
                <p class="{{ $muted }} mb-10">Operational Nodes</p>
                <div class="space-y-4">
                    @if(!empty($supportEmail))
                        <a href="mailto:{{ $supportEmail }}" class="group flex items-center justify-between p-6 rounded-[2rem] bg-slate-900 text-white shadow-xl hover:scale-[1.02] transition-all">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-white/10"><i class="fas fa-envelope text-xs"></i></div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1">Email Node</p>
                                    <p class="text-[11px] font-black tracking-tight truncate">{{ $supportEmail }}</p>
                                </div>
                            </div>
                            <i class="fas fa-arrow-right text-[10px] text-slate-500 group-hover:text-white transition-colors"></i>
                        </a>
                    @endif
                    @if(!empty($supportPhone))
                        <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}" class="group flex items-center justify-between p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 bg-white dark:bg-white/5 shadow-sm hover:shadow-xl transition-all">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-white/5 text-slate-400"><i class="fas fa-phone text-xs"></i></div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Voice Node</p>
                                    <p class="text-[11px] font-black text-slate-900 dark:text-white tracking-tight">{{ $supportPhone }}</p>
                                </div>
                            </div>
                            <i class="fas fa-arrow-right text-[10px] text-slate-300 group-hover:text-[var(--primary)] transition-colors"></i>
                        </a>
                    @endif
                    
                    @foreach([
                        ['Refund Policy', 'refund-policy', 'fa-rotate-left'],
                        ['Privacy Protocol', 'privacy-policy', 'fa-user-shield'],
                        ['Matrix Terms', 'terms-and-conditions', 'fa-file-contract']
                    ] as [$label, $route, $icon])
                        <a wire:navigate href="{{ route($route) }}" class="group flex items-center justify-between p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 bg-white dark:bg-white/5 shadow-sm hover:shadow-xl transition-all">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-white/5 text-slate-400"><i class="fas {{ $icon }} text-xs"></i></div>
                                <p class="text-[11px] font-black text-slate-900 dark:text-white tracking-tight">{{ $label }}</p>
                            </div>
                            <i class="fas fa-arrow-right text-[10px] text-slate-300 group-hover:text-[var(--primary)] transition-colors"></i>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="{{ $panel }}">
                <p class="{{ $muted }} mb-8">Compliance Artifacts</p>
                <div class="space-y-4">
                    @foreach([
                        ['fa-circle-check', 'Policies are published via the public matrix for acquisition transparency.'],
                        ['fa-circle-check', 'Compliance nodes are audited regularly to align with distribution standards.'],
                        ['fa-circle-check', 'Registry updates are synchronized across all legal nodes immediately.']
                    ] as [$icon, $text])
                        <div class="flex gap-4">
                            <i class="fas {{ $icon }} text-emerald-500 mt-1 text-xs"></i>
                            <p class="text-[10px] font-bold text-slate-400 leading-relaxed">{{ $text }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
