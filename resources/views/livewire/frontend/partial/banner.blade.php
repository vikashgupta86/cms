@if(isset($items) && $items->count())

<div class="container-fluid carousel bg-light px-0">
    <div class="row g-0 justify-content-end">

        <div class="header-carousel owl-carousel bg-light py-0">
 
            @foreach($items as $item)

                @php
                    $bannerImage = null;

                    if ($item->type === 'file' && !empty($item->file)) {
                        $bannerImage = asset('storage/' . $item->file);
                    }

                    $bannerLink = '#';

                    if ($item->type === 'external' && !empty($item->url)) {
                        $bannerLink = $item->url;
                    } elseif (!empty($item->slug)) {
                        $bannerLink = route('show.content', $item->slug);
                    }
                @endphp

                @if($bannerImage)

                    <div class="row g-0 header-carousel-item align-items-center">

                        <div class="carousel-img">

                            <a href="{{ $bannerLink }}"
                               @if($item->opens_new_tab) target="_blank" rel="noopener" @endif>

                                <img
                                    src="{{ $bannerImage }}"
                                    class="img-fluid w-100 banner-image"
                                    alt="{{ $item->name }}"
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
    .header-carousel .header-carousel-item {
    position: relative;
}

.header-carousel .carousel-img {
    overflow: hidden;
}

.header-carousel .banner-image {
    width: 100%;
    height: 660px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.header-carousel .banner-image:hover {
    transform: scale(1.02);
}

.header-carousel .owl-nav {
    position: absolute;
    width: 100%;
    top: 50%;
    transform: translateY(-50%);
}

.header-carousel .owl-prev,
.header-carousel .owl-next {
    position: absolute;
    width: 45px;
    height: 45px;
    background: rgba(0,0,0,0.5) !important;
    color: #fff !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center;
    justify-content: center;
}

.header-carousel .owl-prev {
    left: 20px;
}

.header-carousel .owl-next {
    right: 20px;
}

.header-carousel .owl-dots {
    text-align: center;
    margin-top: 15px;
}

.header-carousel .owl-dot span {
    width: 12px;
    height: 12px;
    background: #28599a !important;
    display: block;
    border-radius: 50%;
    margin: 0 5px;
}

@media(max-width:768px){

    .header-carousel .banner-image{
        height: 300px;
    }

}
</style>