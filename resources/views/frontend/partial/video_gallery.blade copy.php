@if(isset($items) && $items->count())
<div class="vg-section">

    <h5 class="gallery-heading mb-3">
        <i class="fa fa-video-camera me-1"></i> Video Gallery
    </h5>

    <div class="vg-grid">
        @foreach($items->take(5) as $item)
         @if($item->parent_id!=NuLL)
    
            @php
                $videoUrl = $item->url ?? '';
                // Convert YouTube watch URL to embed URL
                if (str_contains($videoUrl, 'youtube.com/watch')) {
                    parse_str(parse_url($videoUrl, PHP_URL_QUERY), $params);
                    $videoUrl = 'https://www.youtube.com/embed/' . ($params['v'] ?? '');
                } elseif (str_contains($videoUrl, 'youtu.be/')) {
                    $videoId  = basename(parse_url($videoUrl, PHP_URL_PATH));
                    $videoUrl = 'https://www.youtube.com/embed/' . $videoId;
                }
            @endphp

            <div class="vg-item">
                @if($videoUrl)
                    <div class="vg-thumb">
                        <iframe src="{{ $videoUrl }}"
                                title="{{ $item->name }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                @elseif($item->file)
                    <div class="vg-thumb vg-thumb--file">
                        <video controls style="width:100%; height:100%;">
                            <source src="{{ asset('storage/' . $item->file) }}">
                        </video>
                    </div>  
                @else
                    <div class="vg-thumb vg-thumb--placeholder">
                        <i class="fa fa-play-circle fa-3x text-white"></i>
                    </div>
                @endif
                {{-- <p class="vg-title">{{ $item->name }}</p> --}}
            </div>
@endif
        @endforeach
    </div>

</div>

<style>
.gallery-heading { color: #fff; font-size: 18px; font-weight: 700; }
.vg-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}
.vg-item {}
.vg-thumb {
    position: relative; padding-top: 56.25%;
    background: #000; border-radius: 4px; overflow: hidden;
}
.vg-thumb iframe,
.vg-thumb video {
    position: absolute; top: 0; left: 0;
    width: 100%; height: 100%; border: none;
}
.vg-thumb--placeholder {
    display: flex; align-items: center; justify-content: center;
    background: #2a2a2a;
}
.vg-title { color: #fff; font-size: 12px; margin-top: 4px; margin-bottom: 0; line-height: 1.3; }
</style>
@endif
