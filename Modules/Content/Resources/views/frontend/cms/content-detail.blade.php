@extends('frontend.layouts.app')

@section('title', $content->meta_title ?: $content->title)

@if($content->meta_description)
@section('meta_description', $content->meta_description)
@endif

@section('content')
<div class="container py-5">

    {{-- Back link --}}
    <div class="mb-4">
        <a href="{{ url($path) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            {{-- Breadcrumb meta --}}
            <p class="text-muted small mb-2">
                <a href="{{ url($path) }}" class="text-muted text-decoration-none">{{ $menu->name }}</a>
                @if($content->published_at)
                    &nbsp;·&nbsp; {{ $content->published_at->format('d M Y') }}
                @endif
            </p>

            <h1 class="mb-4">{{ $content->title }}</h1>

            <div class="content-body">
                {!! $content->body !!}
            </div>

        </div>
    </div>

</div>
@endsection
