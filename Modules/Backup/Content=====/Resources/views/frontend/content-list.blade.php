<h1>{{ $menu->name }}</h1>

@foreach($contents as $item)
    <a href="{{ url($menu->full_slug.'/'.$item->slug) }}">
        {{ $item->title }}
    </a><br>
@endforeach

{{ $contents->links() }}
