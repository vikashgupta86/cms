@if(isset($items) && $items->count())
<div class="container-fluid announcement-wrapper">
    <div class="row tickerpanle_maine">
  
        <div class="col-md-2 tickerpanle_maine_heading">
            Announcement <i class="fa fa-bullhorn ic_ht"></i>
        </div>

        <div class="col-md-9 Announcement_link">
            <div class="marquee-container">
                <div class="marquee-texts">
                    
                    @foreach($items as $item)
                    
                        @php
                            if ($item->type === 'file' && $item->file) {
                                $url  = asset('storage/' . $item->file);
                                $icon = '<i class="fa fa-file-pdf-o text-danger ms-1"></i>';
                            } elseif ($item->type === 'external' && $item->url) {
                                $url  = $item->url;
                                $icon = '<i class="fa fa-external-link ms-1"></i>';
                            } else {
                                $url  = route('show.content', $item->slug);
                                $icon = '';
                            }
                        @endphp
                        <a href="{{ $url }}" target="_blank" rel="noopener">
                            ↠ {{ $item->name }} {!! $icon !!}
                        </a>
                        &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-1 tickerpanle_maine_view">
            <a href="#">View All</a>
        </div>

    </div>
</div>

<style>
.announcement-wrapper { background: #000; padding: 0; }
.tickerpanle_maine {
    background: #000; color: #fff;
    min-height: 50px; display: flex;
    align-items: stretch; overflow: hidden; margin: 0;
}
.tickerpanle_maine_heading {
    background: #2b2b2b; color: #fff; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    gap: 8px; padding: 10px 12px; min-height: 50px;
    white-space: nowrap;
}
.ic_ht { font-size: 18px; }
.Announcement_link {
    background: #000; overflow: hidden;
    display: flex; align-items: center;
    min-height: 50px; padding: 0;
}
.marquee-container { width: 100%; overflow: hidden; white-space: nowrap; }
.marquee-texts {
    display: inline-block; white-space: nowrap;
    padding-left: 100%;
    animation: announcement-marquee 40s linear infinite;
}
.marquee-texts a {
    color: #fff; text-decoration: none;
    font-size: 14px; line-height: 50px;
}
.marquee-texts a:hover { color: #f5c542; text-decoration: underline; }
.marquee-container:hover .marquee-texts { animation-play-state: paused; }
.tickerpanle_maine_view {
    background: #2b2b2b; color: #fff; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    min-height: 50px; padding: 10px;
}
.tickerpanle_maine_view a { color: #fff !important; text-decoration: none !important; }
.tickerpanle_maine_view a:hover { color: #f5c542 !important; }
@keyframes announcement-marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}
</style>
@endif
