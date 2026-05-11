@if(isset($items) && $items->count())

<div class="container-fluid carousel bg-light px-0">
    <div class="row g-0 justify-content-end">

        <div class="header-carousel owl-carousel owl-theme bg-light py-0">

            @foreach($items as $item)

                @php
                    $bannerImage = null;

                    if ($item->type === 'file' && !empty($item->file)) {
                        $bannerImage = asset('storage/' . $item->file);
                    }

                    $bannerLink = '#';

                    if ($item->type === 'external' && !empty($item->url)) {
                        $bannerLink = $item->url;
                    }
                @endphp

                @if($bannerImage)

                    <div class="header-carousel-item">
                        <div class="carousel-img">

                            <a href="{{ $bannerLink }}"
                               @if($item->opens_new_tab || $item->type === 'external') target="_blank" rel="noopener" @endif>

                                <img
                                    src="{{ $bannerImage }}"
                                    class="img-fluid w-100 banner-image"
                                    alt="{{ $item->name ?? 'Banner' }}"
                                >

                            </a>

                        </div>
                    </div>

                @endif

            @endforeach

        </div>

    </div>
</div>

@endif


<style>
.header-carousel {
    display: block;
    width: 100%;
}

.header-carousel .header-carousel-item {
    position: relative;
    width: 100%;
}

.header-carousel .carousel-img {
    width: 100%;
    overflow: hidden;
}

.header-carousel .banner-image {
    width: 100%;
    height: 660px;
    object-fit: cover;
    display: block;
}

.header-carousel.owl-loaded {
    display: block;
}

.header-carousel .owl-stage-outer,
.header-carousel .owl-stage,
.header-carousel .owl-item {
    height: auto;
}

.header-carousel .owl-nav {
    margin-top: 0;
}

.header-carousel .owl-prev,
.header-carousel .owl-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);

    width: 45px;
    height: 45px;

    background: rgba(0,0,0,0.5) !important;
    color: #fff !important;

    border-radius: 50% !important;
}

.header-carousel .owl-prev {
    left: 20px;
}

.header-carousel .owl-next {
    right: 20px;
}

.header-carousel .owl-dots {
    position: absolute;
    bottom: 20px;
    width: 100%;
    text-align: center;
}

.header-carousel .owl-dot span {
    width: 12px !important;
    height: 12px !important;
    background: #28599a !important;
}

@media(max-width:768px){
    .header-carousel .banner-image {
        height: 300px;
    }
}
</style>


<script>
document.addEventListener('DOMContentLoaded', function () {

    if (typeof $ !== 'undefined' && $.fn.owlCarousel) {

        $('.header-carousel').owlCarousel({
            items: 1,
            loop: true,
            margin: 0,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            smartSpeed: 800,
            navText: [
                '<i class="bi bi-arrow-left"></i>',
                '<i class="bi bi-arrow-right"></i>'
            ]
        });

    } else {
        console.error('Owl Carousel not loaded. Please include owl.carousel.min.js and owl.carousel.min.css');
    }

});
</script>