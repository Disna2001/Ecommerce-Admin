<div class="space-y-6">
    <x-admin.movements.summary :stats="$stats" />
    <x-admin.movements.filters :stats="$stats" :contexts="$contexts" />
    <x-admin.movements.table :movements="$movements" />

    @if($showDetailModal && $selectedMovement)
        <x-admin.movements.detail-modal :selected-movement="$selectedMovement" />
    @endif
</div>
