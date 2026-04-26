@props(['menu', 'level' => 0])

@php
    $children = $menu->relationLoaded('childrenRecursive') ? $menu->childrenRecursive : $menu->children;
    $padding = 1.25 + ($level * 1.25);
@endphp

<a href="{{ route('cms.show', $menu->full_slug) }}"
   class="block py-2 text-sm font-medium text-gray-700 hover:text-gov-navy"
   style="padding-left: {{ $padding }}rem;">{{ $menu->title }}</a>

@if($children->isNotEmpty())
    @foreach($children as $child)
        @include('components.mobile-menu-item', ['menu' => $child, 'level' => $level + 1])
    @endforeach
@endif
