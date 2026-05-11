@if(isset($items) && $items->count())
<div class="container-fluid py-3 bg-white border-top">
    <div class="container">
        <div class="d-flex align-items-center gap-3 flex-wrap">

            <span class="fw-bold text-dark" style="font-size:14px;">Follow Us:</span>

            @foreach($items as $item)
                @php
                    $href = $item->url ?? '#';
                    $iconMap = [
                        'facebook'  => 'fa fa-facebook',
                        'twitter'   => 'fa fa-twitter',
                        'youtube'   => 'fa fa-youtube',
                        'instagram' => 'fa fa-instagram',
                        'linkedin'  => 'fa fa-linkedin',
                    ];
                    $iconClass = $item->icon ?? $iconMap[strtolower($item->name)] ?? 'fa fa-share-alt';
                @endphp
                <a href="{{ $href }}" target="_blank" rel="noopener"
                   class="social-link" title="{{ $item->name }}">
                    <i class="{{ $iconClass }}"></i>
                    <span>{{ $item->name }}</span>
                </a>
            @endforeach

        </div>
    </div>
</div>

<style>
.social-link {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 20px;
    background: #1a3a6b; color: #fff;
    text-decoration: none; font-size: 13px;
    transition: background 0.2s;
}
.social-link:hover { background: #28599a; color: #fff; }
.social-link i { font-size: 15px; }
</style>
@endif
