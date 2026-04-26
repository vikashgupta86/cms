@extends('frontend.layouts.app')

@section('title', $content->meta_title ?: $content->title)

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('cms.show', $menuPath) }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h1 class="mb-2">{{ $content->title }}</h1>
            <p class="text-muted small mb-4">
                {{ $menuTitle }}
                @if($content->published_at)
                    · Published {{ $content->published_at->format('d M Y h:i A') }}
                @endif
            </p>

            <div>
                {!! $content->body !!}
            </div>
        </div>
    </div>
</div>
@endsection
