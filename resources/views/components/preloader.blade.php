@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    $logoPath = \App\Models\SiteSetting::get('logo_path', '');
    $primaryColor = \App\Models\SiteSetting::get('primary_color', '#8b5cf6');
@endphp

<div id="global-preloader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white dark:bg-slate-950 transition-opacity duration-700 ease-in-out">
    <div class="relative flex flex-col items-center">
        <!-- Background Glow -->
        <div class="absolute inset-0 -z-10 h-32 w-32 -translate-x-1/2 -translate-y-1/2 rounded-full bg-violet-500/20 blur-3xl animate-pulse"></div>

        <!-- Logo/Icon Container -->
        <div class="relative mb-8 h-20 w-20 sm:h-24 sm:w-24">
            <!-- Animated Ring -->
            <div class="absolute inset-0 rounded-full border-2 border-slate-100 dark:border-white/5"></div>
            <div class="absolute inset-0 rounded-full border-t-2 border-violet-500 animate-spin" style="border-top-color: {{ $primaryColor }}"></div>
            
            <!-- Logo -->
            <div class="absolute inset-0 flex items-center justify-center p-4">
                @if(!empty($logoPath))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-full w-full object-contain">
                @else
                    <span class="text-2xl font-black lowercase text-slate-900 dark:text-white">{{ substr($siteName, 0, 1) }}</span>
                @endif
            </div>
        </div>

        <!-- Website Name -->
        <div class="flex flex-col items-center gap-4">
            <h2 class="animate-preloader-name text-3xl sm:text-4xl font-black lowercase tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-slate-900 to-slate-700 dark:from-white dark:to-slate-400">
                {{ $siteName }}
            </h2>
            <div class="h-1 w-48 rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                <div id="preloader-bar" class="h-full w-0 bg-gradient-to-r from-violet-500 to-fuchsia-500 transition-all duration-500" style="background: linear-gradient(90deg, {{ $primaryColor }}, #d946ef)"></div>
            </div>
            <p id="preloader-text" class="text-[10px] font-bold tracking-[0.3em] uppercase text-slate-400 dark:text-slate-500">INITIALIZING</p>
        </div>
    </div>
</div>

<style>
    @keyframes preloader-name-fade {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-preloader-name {
        animation: preloader-name-fade 0.8s ease-out forwards;
    }
    body.preloader-active {
        overflow: hidden;
    }
    #global-preloader.fade-out {
        opacity: 0;
        pointer-events: none;
    }
</style>

<script>
    (function() {
        const preloader = document.getElementById('global-preloader');
        const bar = document.getElementById('preloader-bar');
        const text = document.getElementById('preloader-text');
        
        if (!preloader) return;

        document.body.classList.add('preloader-active');

        // Progress simulation
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 90) {
                progress = 90;
                clearInterval(interval);
            }
            if (bar) bar.style.width = progress + '%';
        }, 100);

        function hidePreloader() {
            clearInterval(interval);
            if (bar) bar.style.width = '100%';
            if (text) text.innerText = 'READY';
            
            setTimeout(() => {
                preloader.classList.add('fade-out');
                document.body.classList.remove('preloader-active');
            }, 500);
        }

        // Hide when DOM is ready
        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', hidePreloader);
        }

        // Livewire integration
        document.addEventListener('livewire:navigate', () => {
            preloader.classList.remove('fade-out');
            document.body.classList.add('preloader-active');
            if (bar) bar.style.width = '0%';
            if (text) text.innerText = 'LOADING';
            
            let navProgress = 0;
            const navInterval = setInterval(() => {
                navProgress += 10;
                if (navProgress >= 80) clearInterval(navInterval);
                if (bar) bar.style.width = navProgress + '%';
            }, 50);
        });

        document.addEventListener('livewire:navigated', () => {
            hidePreloader();
        });
    })();
</script>
