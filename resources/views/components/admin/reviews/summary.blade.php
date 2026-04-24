@php
    $stats = $this->stats;
    $cards = [
        ['label' => 'Total', 'value' => $stats['total'], 'hint' => 'All submitted reviews', 'tone' => 'slate'],
        ['label' => 'Approved', 'value' => $stats['approved'], 'hint' => 'Visible on product pages', 'tone' => 'emerald'],
        ['label' => 'Pending', 'value' => $stats['pending'], 'hint' => 'Waiting for moderation', 'tone' => 'amber'],
        ['label' => 'Flagged', 'value' => $stats['flagged'], 'hint' => 'Needs operator attention', 'tone' => 'blue'],
        ['label' => 'Average Score', 'value' => $stats['avg'] . ' / 5', 'hint' => 'Customer satisfaction signal', 'tone' => 'accent'],
        ['label' => 'Five Stars', 'value' => $stats['five'], 'hint' => 'Highest-rated reviews', 'tone' => 'slate'],
        ['label' => 'One Star', 'value' => $stats['one'], 'hint' => 'Low-rated reviews', 'tone' => 'slate'],
    ];
@endphp

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-7">
    @foreach($cards as $card)
        <x-admin.ui.metric :label="$card['label']" :value="$card['value']" :hint="$card['hint']" :tone="$card['tone']" />
    @endforeach
</div>
