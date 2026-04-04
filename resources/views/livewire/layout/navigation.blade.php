<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="border-b border-gray-200 bg-white shadow-sm">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center gap-6">
            <a href="{{ route('dashboard') }}" wire:navigate class="text-base font-semibold text-slate-900">
                {{ \App\Models\SiteSetting::get('site_name', config('app.name', 'Laravel')) }}
            </a>
            <div class="hidden items-center gap-4 sm:flex">
                <a href="{{ route('dashboard') }}" wire:navigate class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Dashboard</a>
                <a href="{{ route('profile.index') }}" wire:navigate class="text-sm font-medium text-slate-600 transition hover:text-slate-900">Profile</a>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="hidden text-sm text-slate-500 sm:inline">{{ auth()->user()?->name }}</span>
            <button wire:click="logout" type="button" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                Log Out
            </button>
        </div>
    </div>
</nav>
