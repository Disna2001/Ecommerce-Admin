<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-3xl border border-rose-200 bg-rose-50/90 px-5 py-4 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <x-admin.users.hero
        :attention-queues="$attentionQueues"
        :filtered-users="$filteredUsers"
        :total-users="$totalUsers"
        :verified-users="$verifiedUsers"
        :roles="$roles"
        :selected-role="$selectedRole"
        :status-filter="$statusFilter"
    />

    <section class="grid gap-6 2xl:grid-cols-[1.8fr_0.8fr]">
        <div class="space-y-6">
            <x-admin.users.filters :roles="$roles" />
            <x-admin.users.table :users="$users" />
        </div>

        <x-admin.users.sidebar :recent-access-changes="$recentAccessChanges" :roles="$roles" />
    </section>

    @if($selectedUser)
        <x-admin.users.user-modal :selected-user="$selectedUser" :roles="$roles" />
    @endif

    <div wire:loading.delay class="fixed bottom-4 right-4 z-50 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-lg dark:bg-white dark:text-slate-900">
        Updating access workspace...
    </div>
</div>
