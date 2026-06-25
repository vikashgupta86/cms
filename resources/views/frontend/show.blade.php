@extends("frontend.layouts.app")

@section("title")
    {{ $content->meta_title ?? $content->name ?? app_name() }}
@endsection
@section('content')
    <section class="inner-page" id="inner-search">
        <div class="container">
            {{-- ✅ Breadcrumb --}}
            <ul class="breadcrumb">
                <li><a href="{{ url('/') }}">Home</a></li>

                @if($content->parent)
                    <li>
                        <a href="{{ route('show.content', $content->parent->slug) }}">
                            {{ $content->parent->name }}
                        </a>
                    </li>
                @endif

                <li>
                    <span>{{ $content->name }}</span>
                </li>
            </ul>

            @php
                // 👇 If your column isn't called "image", just change this one line
                $bgImage = $content->image ?? null;

                $jumboStyle = $bgImage
                    ? "background-image: url('" . asset($bgImage) . "');"
                    : "background-color: #034903;";
            @endphp

            <div class="container-fluid" id="jumbo" style="
                {{ $jumboStyle }}
                width: 100%;
                padding: 20px !important;
                background-size: cover;
                background-position: center;
            ">
                <div class="row align-items-center justify-content-center" style="height: 250px">
                    <div class="col-md-2" style="
                                height: 230px;
                                width: 70px;

                                border-top: 10px solid white;
                                border-bottom: 10px solid white;
                                border-left: 10px solid white;
                            "></div>
                    <div class="col-md-10 my-auto" style="
                                background-color: rgba(3, 73, 3, 0.6);
                                padding: 20px;
                                margin-left: -20px;
                            ">
                        <h2 class="animated slideInDown" style="color: orangered;font-weight:700">
                            {{ $content->meta_title ?? $content->name }}
                        </h2>
                        <h3 class="display-5 animated slideInDown" style="color: white">
                            {{ $content->name }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- LEFT SIDEBAR --}}
                <!-- <div class="col-md-3">
                    <ul class="sidebar-links">

                        @foreach($children as $child)
                            @php
                                $childUrl = $child->type === 'file'
                                    ? asset($child->file)
                                    : ($child->type === 'external'
                                        ? $child->getFullUrl()
                                        : route('show.content', $child->slug));

                                $childTarget = ($child->type === 'file' || $child->opens_new_tab) ? '_blank' : '_self';
                            @endphp

                            <li>
                                <a href="{{ $childUrl }}" target="{{ $childTarget }}">
                                    {{ $child->name }}

                                    @if($child->type === 'file')
                                        <i class="fa fa-file-pdf-o"></i>
                                    @elseif($child->type === 'external')
                                        <i class="fa fa-external-link"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach

                    </ul>
                </div> -->

                {{-- RIGHT CONTENT --}}
                <div class="col-md-9">

                    {{-- Last Updated --}}
                    <div class="text-end smallfont mb-2">
                        Last Updated On:
                        {{ optional($content->updated_at)->format('d F Y') }}
                    </div>

                    {{-- Heading --}}
                    <h1 class="innerheading">
                        {{ $content->name }}
                    </h1>

                    {{-- Content --}}
                    <div class="cms-content">
                        {!! $content->content !!}
                    </div>




                    @if(isset($submenu) && $submenu->count())
                        <div class="col-md-12">
                            <table id="customers" class="table table-hover tablecont">
                                <thead>
                                    <tr>
                                        <th>S. No.</th>
                                        <th style="width: 540px;">Title</th>
                                        <th>Link Type</th>
                                        <th>File Size</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($submenu as $index => $child)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>

                                            <td>
                                                @if($child->type === 'file')
                                                    <a href="{{ asset($child->file) }}"
                                                        target="{{ $child->opens_new_tab ? '_blank' : '_self' }}">{{ $child->name }}</a>
                                                @elseif($child->type === 'external')
                                                    <a href="{{ $child->getFullUrl() }}"
                                                        target="{{ $child->opens_new_tab ? '_blank' : '_self' }}">{{ $child->name }}</a>

                                                @else
                                                    <a href="{{ route('show.content', $child->slug) }}">{{ $child->name }}</a>
                                                @endif
                                            </td>

                                            <td>
                                                @if($child->type === 'file')
                                                    File
                                                @elseif($child->type === 'external')
                                                    Link
                                                @else
                                                    Content
                                                @endif
                                            </td>

                                            <td style="color: red;">
                                                @if($child->type === 'file' && !empty($child->file_size))
                                                    {{ number_format($child->file_size / 1024, 2) }} KB
                                                @endif
                                            </td>

                                            <td>
                                                {{ optional($child->updated_at)->format('d F Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>


@endsection