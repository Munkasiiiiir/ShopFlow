@props(['href', 'active' => false])

@php
$classes = ($active ?? false)
    ? 'sf-sidebar-link sf-sidebar-link-active'
    : 'sf-sidebar-link';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @isset($icon)
        <span class="flex h-5 w-5 shrink-0 items-center justify-center opacity-80">{{ $icon }}</span>
    @endisset
    <span class="truncate">{{ $slot }}</span>
</a>
