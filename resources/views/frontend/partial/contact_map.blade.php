@if(isset($items) && $items->count())
<div class="container-fluid bg-light py-5">
    <div class="container">

        @foreach($items as $item)
            <div class="mb-4">
                <h5 class="text-primary mb-3">
                    <i class="fa fa-map-marker me-1"></i> {{ $item->name }}
                </h5>

                @if($item->content)
                    <div class="mb-3">{!! $item->content !!}</div>
                @endif

                @php
                    $mapEmbed = data_get($item->custom_data, 'map_embed');
                @endphp

                @if($mapEmbed)
                    <div class="ratio ratio-16x9 rounded overflow-hidden border">
                        {!! $mapEmbed !!}
                    </div>
                @endif
            </div>
        @endforeach

    </div>
</div>
@endif
