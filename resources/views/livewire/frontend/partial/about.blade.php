@if(isset($items) && $items->count())
<div class="container-fluid bg-light py-5">
    <div class="container">

        @foreach($items as $item)
            @if($item->type === 'content')

                <div class="row align-items-start">

                    {{-- About text col --}}
                    <div class="col-md-8">
                        <h4 class="text-primary mb-3">
                            @if($item->file_icon)
                                <img src="{{ asset('storage/' . $item->file_icon) }}"
                                     alt="{{ $item->name }}"
                                     style="height:40px; margin-right:8px; vertical-align:middle;">
                            @endif
                            {{ $item->name }}
                        </h4>

                        <div class="about-content text-dark">
                            {!! $item->content !!}
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('show.content', $item->slug) }}"
                               class="btn btn-primary rounded-pill py-2 px-4">
                                View more ➜
                            </a>
                        </div>
                    </div>

                    {{-- Children — minister cards --}}
                    @if($item->children->count())
                        <div class="col-md-4">
                            <div class="row g-3">
                                @foreach($item->children as $child)
                                    <div class="col-6">
                                        <div class="minister-card text-center border rounded overflow-hidden shadow-sm">
                                            @if($child->file)
                                                <img src="{{ asset('storage/' . $child->file) }}"
                                                     class="img-fluid w-100"
                                                     alt="{{ $child->name }}"
                                                     style="height:180px; object-fit:cover; object-position:top;">
                                            @endif
                                            <div class="p-2 bg-light">
                                                <p class="mb-0 fw-bold" style="font-size:13px;">{{ $child->name }}</p>
                                                @if($child->description)
                                                    <p class="mb-0 text-muted" style="font-size:11px;">{{ $child->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

            @endif
        @endforeach

    </div>
</div>

<style>
.about-content { font-size: 15px; line-height: 1.8; }
.about-content p { margin-bottom: 10px; }
.minister-card { transition: transform 0.2s; }
.minister-card:hover { transform: translateY(-3px); }
</style>
@endif
