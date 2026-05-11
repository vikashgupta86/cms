<div class="container-fluid footer bg-light py-3">
    <div class="container py-5">

        <div class="row g-5 align-items-start">

            {{-- QUICK LINKS --}}
            <div class="col-md-6 col-lg-3">

                <div class="footer-item">

                    <h5 class="footer-heading mb-4">
                        <i class="fas fa-link me-2"></i>
                        Quick Links
                    </h5>

                    @foreach($items as $item)

                        @php
                            $url = '#';

                            if ($item->type === 'external' && !empty($item->url)) {

                                $url = $item->url;

                            } elseif ($item->type === 'file' && !empty($item->file)) {

                                $url = asset('storage/' . $item->file);

                            } elseif (!empty($item->slug)) {

                                $url = route('show.content', $item->slug);
                            }
                        @endphp

                        <a href="{{ $url }}"
                           class="footer-link d-block"
                           @if($item->opens_new_tab) target="_blank" @endif>

                            <i class="fas fa-angle-right me-2"></i>

                            {{ $item->name }}

                        </a>

                    @endforeach

                </div>

            </div>

            {{-- CONTACT --}}
            <div class="col-md-6 col-lg-3">

                <div class="footer-item">

                    <h5 class="footer-heading mb-4">
                        <i class="fas fa-phone-volume me-2"></i>
                        Contact Us
                    </h5>
                  {{-- @dd($contact_us); --}}

                    @if($contact = $contact_us['data']->first())
                        {!! $contact->content !!}
                    @endif
                {{-- <p class="mb-2">                        
                        <strong>Bureau of Energy Efficiency</strong>
                    </p>
                       @dd($contact_us)
                    <p class="footer-contact-text">
                        Ministry of Power, Govt. of India<br>
                        4th Floor, Sewa Bhawan<br>
                        R. K. Puram, New Delhi - 110066 (INDIA)<br>
                        Fax: +91 11 26178352
                    </p> --}}

                    {{-- SOCIAL ICONS --}}
                    @if(isset($socialItems) && $socialItems->count())

                        <div class="d-flex pt-3 flex-wrap">

                            @foreach($socialItems as $social)

                                <a class="btn btn-square btn-primary me-2 mb-2"
                                   href="{{ $social->url }}"
                                   target="_blank">

                                    @if(Str::contains(strtolower($social->name), 'facebook'))

                                        <i class="fab fa-facebook-f"></i>

                                    @elseif(Str::contains(strtolower($social->name), 'instagram'))

                                        <i class="fab fa-instagram"></i>

                                    @elseif(Str::contains(strtolower($social->name), 'youtube'))

                                        <i class="fab fa-youtube"></i>

                                    @elseif(Str::contains(strtolower($social->name), 'twitter') || Str::contains(strtolower($social->name), 'x'))

                                        <i class="fab fa-twitter"></i>

                                    @else

                                        <i class="fas fa-link"></i>

                                    @endif

                                </a>

                            @endforeach

                        </div>

                    @endif

                </div>

            </div>

            {{-- MAP --}}
            <div class="col-md-12 col-lg-6">

                <div class="footer-item">

                    <h5 class="footer-heading mb-4">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Location Map
                    </h5>
               

                   @if(isset($map_section['data']) && $map_section['data']->count())

    @foreach($map_section['data'] as $map)

        <iframe
            src="{{ $map->content }}"
            width="100%"
            height="220"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>

    @endforeach

@endif

                </div>

            </div>

        </div>

    </div>
</div>

<style>

.footer-heading {
    font-size: 28px;
    font-weight: 700;
    color: #1f5597;
    display: flex;
    align-items: center;
}

.footer-link {
    color: #111;
    text-decoration: none;
    margin-bottom: 10px;
    transition: 0.3s;
    font-size: 14px;
}

.footer-link:hover {
    color: #1f5597;
    padding-left: 5px;
}

.footer-contact-text {
    font-size: 15px;
    /* line-height: 1.8; */
}

.btn-square {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

</style>