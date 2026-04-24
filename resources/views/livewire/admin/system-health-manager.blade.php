<div class="space-y-6">
    <x-admin.health.hero :score="$score" :score-tone="$scoreTone" :metrics="$metrics" />

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="space-y-6">
            <x-admin.health.core-checks :checks="$checks" />
            <x-admin.health.attention :attention="$attention" />
        </div>

        <div class="space-y-6">
            <x-admin.health.operator-actions />
            <x-admin.health.checklist :checklist="$checklist" />
            <x-admin.health.commands :deploy-commands="$deployCommands" />
            <x-admin.health.signals :recent-signals="$recentSignals" />
        </div>
    </div>
</div>
