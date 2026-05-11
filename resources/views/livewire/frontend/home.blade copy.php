@extends('frontend.layouts.app')

@section('title', app_name())

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
@dd($sectionsData);
<section class="home-page">
     @forelse($sectionsData as $section)
 

        @if($section['section_type'] === 'announcement')

            @include('livewire.frontend.partial.announcement', [
                'items' => $section['data']
            ])
        @elseif($section['section_type'] === 'portal')

            @include('livewire.frontend.partial.portal', [
                'items' => $section['data']
            ])

            @elseif($section['section_type'] === 'gov-')

            @include('livewire.frontend.partial.portal', [
                'items' => $section['data']
            ]) 

              @elseif($section['section_type'] === 'minister')

            @include('livewire.frontend.partial.pmquote', [
                'items' => $section['data']
            ])

                          @elseif($section['section_type'] === 'about')

            @include('livewire.frontend.partial.about', [
                'items' => $section['data']
            ])

                         @elseif($section['section_type'] === 'news')

            @include('livewire.frontend.partial.news', [
                'items' => $section['data']
            ])

        @else

            <div class="container py-4">
                <div class="mb-4">
                    <h3 class="mb-3">
                        {{ ucfirst(str_replace('_', ' ', $section['section_type'])) }}
                    </h3>

                    <div class="row">
                        @forelse($section['data'] as $item)

                            <div class="col-md-4 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">

                                        <h5 class="card-title">
                                            {{ $item->name }}
                                        </h5>

                                        @if($item->type === 'content')
                                            <p>
                                                {{ Str::limit(strip_tags($item->content), 150) }}
                                            </p>

                                            <a href="{{ route('show.content', $item->slug) }}"
                                               class="btn btn-sm btn-primary">
                                                Read More
                                            </a>
                                        @endif

                                        @if($item->type === 'file' && $item->file)
                                            <a href="{{ asset('storage/' . $item->file) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-danger">
                                                View File
                                            </a>
                                        @endif

                                        @if($item->type === 'external' && $item->url)
                                            <a href="{{ $item->url }}"
                                               target="_blank"
                                               class="btn btn-sm btn-info">
                                                Open Link
                                            </a>
                                        @endif

                                    </div>
                                </div>
                            </div>

                        @empty
                            <div class="col-12">
                                <p>No data found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        @endif

    @empty

        <div class="container py-4">
            <div class="alert alert-warning">
                No homepage section found.
            </div>
        </div>

    @endforelse

</section>

@endsection