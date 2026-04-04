@if($sortField === $field)
    @if($sortDirection === 'asc')
        <span class="ml-1">↑</span>
    @else
        <span class="ml-1">↓</span>
    @endif
@endif