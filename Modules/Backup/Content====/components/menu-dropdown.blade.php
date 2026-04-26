@props(['menu', 'depth' => 0])

@php
    $children = $menu->relationLoaded('childrenRecursive') ? $menu->childrenRecursive : $menu->children;
    $panelPlacement = $depth === 0
        ? 'absolute top-full left-0 mt-1'
        : 'absolute top-0 left-full ml-1';
@endphp

<div class="relative group">
    <a href="{{ route('cms.show', $menu->full_slug) }}"
       class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:text-gov-navy rounded-lg hover:bg-gray-50 transition-colors whitespace-nowrap">
        <span class="truncate">{{ $menu->title }}</span>
        @if($children->isNotEmpty())
            <svg class="w-4 h-4 text-gray-400 group-hover:text-gov-navy transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $depth === 0 ? 'M19 9l-7 7-7-7' : 'M9 5l7 7-7 7' }}"/>
            </svg>
        @endif
    </a>

    @if($children->isNotEmpty())
        <div class="{{ $panelPlacement }} min-w-[18rem] bg-white border border-gray-200 rounded-2xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
            <div class="py-1">
                @foreach($children as $child)
                    <div class="relative group">
                        <a href="{{ route('cms.show', $child->full_slug) }}"
                           class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:text-gov-navy hover:bg-gray-50 transition-colors whitespace-nowrap">
                            <span class="truncate">{{ $child->title }}</span>
                            @if(($child->relationLoaded('childrenRecursive') ? $child->childrenRecursive : $child->children)->isNotEmpty())
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-gov-navy transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </a>
                        @if(($child->relationLoaded('childrenRecursive') ? $child->childrenRecursive : $child->children)->isNotEmpty())
                            @include('components.menu-dropdown', ['menu' => $child, 'depth' => $depth + 1])
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
