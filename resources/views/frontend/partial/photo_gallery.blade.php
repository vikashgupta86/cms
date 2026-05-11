@if(isset($items) && $items->count())

@php
    // Parent menu item for clickable title
    $parentGallery = $items->whereNull('parent_id')->first();

    $galleryUrl = ($parentGallery && !empty($parentGallery->slug))
        ? route('show.content', $parentGallery->slug)
        : '#';
@endphp

<div class="col-md-6">

    {{-- Heading --}}
    <h5 class="gallery-title">
        <i class="fa fa-picture-o me-2"></i>

        <a href="{{ $galleryUrl }}" class="header_link">
            Photo Gallery
        </a>
    </h5>

    {{-- Wrapper --}}
    <div class="pg-wrapper">

        <div id="photoCarousel"
             class="carousel slide h-100"
             data-bs-ride="carousel"
             data-bs-interval="3000">

            {{-- Slides --}}
            <div class="carousel-inner h-100">

                @foreach($items->whereNotNull('parent_id') as $photo)

                    @php
                        $src = !empty($photo->file)
                            ? asset('storage/' . ltrim($photo->file, '/'))
                            : (!empty($photo->file_icon)
                                ? asset('storage/' . ltrim($photo->file_icon, '/'))
                                : asset('images/placeholder.jpg'));

                        $photoUrl = !empty($photo->slug)
                            ? route('show.content', $photo->slug)
                            : '#';
                    @endphp

                    <div class="carousel-item h-100 {{ $loop->first ? 'active' : '' }}">

                        <a href="{{ $photoUrl }}" class="pg-link">

                            <img src="{{ $src }}"
                                 class="pg-img"
                                 alt="{{ $photo->name }}"
                                 onerror="this.src='{{ asset('images/placeholder.jpg') }}'">

                            <div class="pg-caption">
                                {{-- {{ $photo->name }} --}}
                            </div>

                        </a>

                    </div>

                @endforeach

            </div>

            {{-- Bottom right arrows --}}
            <div class="pg-nav">

                <button class="pg-btn"
                        type="button"
                        data-bs-target="#photoCarousel"
                        data-bs-slide="prev"
                        aria-label="Previous">

                    <span class="carousel-control-prev-icon"></span>

                </button>

                <button class="pg-btn"
                        type="button"
                        data-bs-target="#photoCarousel"
                        data-bs-slide="next"
                        aria-label="Next">

                    <span class="carousel-control-next-icon"></span>

                </button>

            </div>

        </div>

    </div>

</div>
<style>
    
</style>
<style>
.gallery-title {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 18px;
    color: #fff;
}

.header_link {
    color: #fff;
    text-decoration: none;
}

.header_link:hover {
    text-decoration: underline;
}

/*
  Match the video grid exactly:
  Video side = 2 cols × 2 rows of ~300px wide thumbnails with ~4px gap = ~604px wide, ~330px tall
  We copy those dimensions so both columns look identical in height and width.
*/
.pg-wrapper {
    width: 163%;          /* fills col-md-6 fully */
    height: 100%;        /* same total height as the 2×2 video grid */
    border-radius: 8px;
    overflow: hidden;
    position: relative;
}

#photoCarousel,
#photoCarousel .carousel-inner,
.carousel-item {
    height: 100% !important;
    border-radius: 8px;
}

.pg-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
    border-radius: 8px;
}

/* Bottom-right nav arrows */
.pg-nav {
    position: absolute;
    bottom: 10px;
    right: 10px;
    display: flex;
    gap: 6px;
    z-index: 20;
}

.pg-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(0,0,0,0.55);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
    padding: 0;
}

.pg-btn:hover {
    background: rgba(0,0,0,0.82);
}

.pg-btn .carousel-control-prev-icon,
.pg-btn .carousel-control-next-icon {
    width: 14px;
    height: 14px;
}

@media (max-width: 767px) {
    .pg-wrapper {
        height: 240px;
    }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endif