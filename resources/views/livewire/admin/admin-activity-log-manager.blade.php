<div class="space-y-6">
    <x-admin.activity.summary :stats="$stats" />
    <x-admin.activity.filters :stats="$stats" :users="$users" />
    <x-admin.activity.table :logs="$logs" />

    @if($showDetailModal && $selectedLog)
        <x-admin.activity.detail-modal :selected-log="$selectedLog" />
    @endif
</div>
