@if(isset($items) && $items->count())

@php
    // LEVEL 1: News & Events parent
    $mainParent = $items->whereNull('parent_id')->first();

    // LEVEL 2: Tabs
    $tabs = $mainParent
        ? $items->where('parent_id', $mainParent->id)->values()
        : collect();
 
    $mainUrl = ($mainParent && !empty($mainParent->slug))
        ? route('show.content', $mainParent->slug)
        : '#';
@endphp

<div class="container py-5">
    <div class="row g-4 align-items-start">

        {{-- LEFT: News & Events --}}
        <div class="col-md-8">

            <a href="{{ $mainUrl }}" class="heading-link">
                <h5 class="news-section-heading mb-2">
                    <span class="heading-sq"></span>
                    {{ $mainParent->name ?? 'News & Events' }}
                </h5>
            </a>

            {{-- LEVEL 2 TABS --}}
            <ul class="nav news-tabs mb-0" id="newsTabs" role="tablist">
                @foreach($tabs as $tab)
                    <li class="nav-item flex-fill" role="presentation">
                        <button
                            class="nav-link w-100 {{ $loop->first ? 'active' : '' }}"
                            id="news-tab-{{ $tab->id }}"
                            data-bs-toggle="tab"
                            data-bs-target="#news-pane-{{ $tab->id }}"
                            type="button"
                            role="tab">
                            {{ $tab->name }}
                        </button>
                    </li>
                @endforeach
            </ul>

         {{-- @php
         echo'<pre>';
            print_r($tabs );
            die;
         @endphp --}}


            {{-- LEVEL 3 DATA --}}
            <div class="tab-content news-tab-content">

           @foreach($tabs as $tab)

    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
         id="news-pane-{{ $tab->id }}"
         role="tabpanel">

        <div class="tabpanel_links">

            @forelse($tab->children as $child)

                @php
                    if ($child->type === 'external' && !empty($child->url)) {
                        $href = $child->url;
                    } elseif ($child->type === 'file' && !empty($child->file)) {
                        $href = asset('storage/' . ltrim($child->file, '/'));
                    } elseif (!empty($child->slug)) {
                        $href = route('show.content', $child->slug);
                    } else {
                        $href = '#';
                    }

                    $isExternal = $child->opens_new_tab || in_array($child->type, ['external', 'file']);
                @endphp

                <a href="{{ $href }}"
                   @if($isExternal) target="_blank" rel="noopener" @endif>
                    <span class="news-item-name">
                        {{ $child->name }}

                        @if($child->type === 'file')
                            <i class="fa fa-file-pdf-o text-danger ms-1"></i>
                        @elseif($child->type === 'external')
                            <i class="fa fa-external-link ms-1"></i>
                        @endif
                    </span>

                    <span class="news-arrow">&#10132;</span>
                </a>

            @empty
                <div class="p-3 text-muted" style="font-size:14px;">
                    No records found.
                </div>
            @endforelse

        </div>
    </div>

@endforeach

            </div>

            <div>
                <a href="{{ $mainUrl }}"class="btn btn-primary rounded-pill py-2 px-4 px-lg-3 mb-3 mb-md-3 mb-lg-0">
                    View more &#10132;
                </a>
            </div>

        </div>

        {{-- RIGHT: Portals --}}
        @if(isset($sidebarItems) && $sidebarItems->count())
            <div class="col-md-4">

                <h5 class="news-section-heading mb-2">
                    <span class="heading-sq"></span>
                    {{ $sidebarTitle ?? 'Portals' }}
                </h5>

                <div class="watsnew_linkspanel">
                    @foreach($sidebarItems as $item)

                        @php
                            if ($item->type === 'external' && !empty($item->url)) {
                                $href = $item->url;
                            } elseif ($item->type === 'file' && !empty($item->file)) {
                                $href = asset('storage/' . ltrim($item->file, '/'));
                            } elseif (!empty($item->slug)) {
                                $href = route('show.content', $item->slug);
                            } else {
                                $href = '#';
                            }

                            $isExternal = in_array($item->type, ['external', 'file'])
                                || $item->opens_new_tab;
                        @endphp

                        <a href="{{ $href }}"
                           @if($isExternal) target="_blank" rel="noopener" @endif>
                            <span class="link-name">{{ $item->name }}</span>
                            <span class="link-arrow">&#10132;</span>
                        </a>

                    @endforeach
                </div>

                <div class="text-center">
                    <a href="{{ url('portals') }}" class="btn btn-primary rounded-pill py-2 px-4 px-lg-3 mb-3 mb-md-3 mb-lg-0">
                        View more &#10132;
                    </a>
                </div>

            </div>
        @endif

    </div>
</div>

<style>
.heading-link {
    text-decoration: none;
}

.news-section-heading {
    font-size: 20px;
    font-weight: 700;
    color: #1a3a6b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.heading-sq {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid #1a3a6b;
    flex-shrink: 0;
}

.news-tabs {
    display: flex;
    border: none;
}

.news-tabs .nav-item {
    flex: 1;
}

.news-tabs .nav-link {
    border-radius: 0;
    border: 1px solid #28599a;
    border-right: none;
    color: #333;
    background: #fff;
    font-size: 14px;
    font-weight: 500;
    padding: 10px 8px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.news-tabs .nav-item:last-child .nav-link {
    border-right: 1px solid #28599a;
}

.news-tabs .nav-link.active {
    background: #28599a;
    color: #fff;
}

.news-tab-content {
    border: 1px solid #28599a;
    border-top: none;
    background: #fff;
    max-height: 260px;
    overflow-y: auto;
}

.tabpanel_links a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    border-bottom: 1px solid rgba(40,89,154,0.18);
    color: #111;
    text-decoration: none;
    font-size: 14px;
}

.tabpanel_links a:hover {
    background: #eef3fb;
    color: #1a3a6b;
}

.news-item-name {
    flex: 1;
    padding-right: 10px;
}

.news-arrow {
    flex-shrink: 0;
    color: #28599a;
    font-size: 16px;
}

.watsnew_linkspanel {
    background: #28599a;
    max-height: 286px;
    overflow-y: auto;
}

.watsnew_linkspanel a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    color: #fff;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.22);
    font-size: 12px;
    /* font-weight: 500; */
}

.watsnew_linkspanel a:hover {
    background: #1d4478;
}

.link-name {
    flex: 1;
    padding-right: 10px;
}

.link-arrow {
    flex-shrink: 0;
    font-size: 18px;
    color: rgba(255,255,255,0.85);
}

.btn-view-more {
    background: #28599a;
    color: #fff !important;
    border-radius: 30px;
    padding: 9px 30px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
}

.btn-view-more:hover {
    background: #1a3a6b;
}
</style>

@endif