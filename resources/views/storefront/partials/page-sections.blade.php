@props(['sections' => [], 'slot' => 'before'])

@foreach($sections as $section)
    @php
        $secType = is_array($section) ? ($section['type'] ?? '') : $section->type;
        $secId = is_array($section) ? ($section['id'] ?? '') : $section->id;
        $secConfig = is_array($section) ? ($section['config'] ?? []) : ($section->config ?? []);
        $secIsActive = is_array($section) ? ($section['is_active'] ?? true) : ($section->is_active ?? true);
        $secSlot = is_array($section) ? ($section['slot'] ?? 'before') : ($section->slot ?? 'before');
    @endphp

    @if($secIsActive && $secSlot === $slot && View::exists("storefront.sections.{$secType}"))
        <div data-section-id="{{ $secId }}" data-section-type="{{ $secType }}" class="storefront-section-wrapper">
            @include("storefront.sections.{$secType}", ['config' => $secConfig, 'sectionId' => $secId])
        </div>
    @endif
@endforeach
