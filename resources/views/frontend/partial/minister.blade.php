@if(isset($section) && $section->menuItem)
<section class="minister-quote-section py-4 bg-light">
    <div class="container">
        <div class="p-4 border rounded bg-white">
            <h3>{{ $section->menuItem->name }}</h3>

            @if(!empty($section->menuItem->description))
                <p class="lead mb-2">{{ $section->menuItem->description }}</p>
            @endif

            @if(!empty($section->menuItem->content))
                <div>{!! $section->menuItem->content !!}</div>
            @endif
        </div>
    </div>
</section>
@endif
