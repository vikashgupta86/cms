@php
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);

    $sectionTypes = [
        'top_header'=>'Top Header',
        'banner' =>'Banner',
        'announcement' => 'Announcement (Ticker)',
        'portal' => 'Portals / Services',
        'minister' => 'Minister Quote',
        'about' => 'About Section',
        'ministers' => 'Ministers List',
        'news' => 'News & Events',
        'sidebar_links' => 'Right Sidebar Links',
        'dashboard' => 'Dashboard Stats',
        'events' => 'Events Calendar',
        'video_gallery' => 'Video Gallery',
        'photo_gallery' => 'Photo Gallery',
        'gov_icons' => 'Gov Icons Marquee',
        'footer_links' => 'Footer Links',
        'social' => 'Social Section',
        'contact_us' => 'Contact Us',
        'map'=>'Map section'
    ];

    $homepage = $item->homepageSection ?? null;
@endphp

<tr id="menu-row-{{ $item->id }}">
    <td>{{ $item->sort_order }}</td>

    <td>
        {!! $indent !!}

        @if($item->icon)
            <i class="{{ $item->icon }}"></i>
        @endif

        {{ $item->name }}

        @if($item->children->count() > 0)
            <span class="badge bg-info ms-1">
                {{ $item->children->count() }} child{{ $item->children->count() > 1 ? 'ren' : '' }}
            </span>
        @endif
    </td>

    <td>
        <span class="badge bg-secondary">{{ ucfirst($item->type) }}</span>
    </td>

    {{-- Section Type --}}
    <td>
        <select class="form-control form-control-sm section-type" data-id="{{ $item->id }}">
            <option value="0">No Section</option>

            @foreach($sectionTypes as $key => $label)
                <option value="{{ $key }}" {{ optional($homepage)->section_type == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </td>

    {{-- Place --}}
    {{-- <td>
        <select class="form-control form-control-sm menu-place" data-id="{{ $item->id }}">
            <option value="">Select Place</option>

            <option value="default" {{ optional($homepage)->place == 'default' ? 'selected' : '' }}>
                Default
            </option>

            <option value="middle" {{ optional($homepage)->place == 'middle' ? 'selected' : '' }}>
                Middle
            </option>

            <option value="footer" {{ optional($homepage)->place == 'footer' ? 'selected' : '' }}>
                Footer
            </option>
        </select>
    </td> --}}

    {{-- Homepage Sort Order --}}
    <td>
        <input
            type="number"
            class="form-control form-control-sm menu-position"
            data-id="{{ $item->id }}"
           value="{{ optional($homepage)->menu_order??'' }}"
            placeholder="Section Order"
            min="0"
        >
    </td>

    <td>
        @if($item->is_active)
            <span class="badge bg-success">{{ __('menu::text.active') }}</span>
        @else
            <span class="badge bg-warning">{{ __('menu::text.inactive') }}</span>
        @endif

        @if($item->is_visible)
            <span class="badge bg-primary">{{ __('menu::text.visible') }}</span>
        @else
            <span class="badge bg-secondary">{{ __('menu::text.hidden') }}</span>
        @endif
    </td>

    <td class="text-center">
        <div class="btn-group btn-group-sm" role="group">
            <a class="btn btn-outline-primary btn-sm" title="View">
                <i class="fas fa-eye"></i>
            </a>

            <button
                type="button"
                class="btn btn-outline-success btn-sm save-placement-btn"
                data-id="{{ $item->id }}"
                title="Save Placement"
            >
                <i class="fas fa-save"></i>
            </button>
        </div>

        <small class="d-block mt-1 save-message-{{ $item->id }}"></small>
    </td>
</tr>

@if($item->children->count() > 0)
    @foreach($item->children->sortBy('sort_order') as $child)
        @include('managehomepage::backend.managehomepages.partials.menu-item-row', [
            'item' => $child,
            'level' => $level + 1
        ])
    @endforeach
@endif