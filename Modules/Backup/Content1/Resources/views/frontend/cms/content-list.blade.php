@extends('frontend.layouts.app')

@section('title', $menu->name ?? 'Content')

@section('content')
<div class="container py-5">

    {{-- Breadcrumb / heading --}}
    <div class="mb-4">
        <h1 class="h3">{{ $menu->name }}</h1>
        @if($menu->description)
            <p class="text-muted">{{ $menu->description }}</p>
        @endif
    </div>

    {{-- Search form --}}
    <form method="GET" action="" class="row g-2 mb-4">
        <div class="col-md-8">
            <input type="text" name="q" value="{{ $search }}"
                   class="form-control" placeholder="Search content…">
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    {{-- Results count --}}
    <p class="text-muted small mb-3">{{ $contents->total() }} item(s) found</p>

    {{-- Content cards --}}
    @forelse($contents as $item)
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-start gap-3">
                <div class="flex-grow-1">
                    <h5 class="mb-1">{{ $item->title }}</h5>
                    <p class="mb-2">
                        <span class="{{ $item->type_badge_class }}">{{ ucfirst($item->type) }}</span>
                        @if($item->published_at)
                            <span class="text-muted small ms-2">{{ $item->published_at->format('d M Y') }}</span>
                        @endif
                    </p>

                    @if($item->type === 'content')
                        <p class="mb-0 text-muted">
                            {{ \Illuminate\Support\Str::limit(strip_tags($item->body), 200) }}
                        </p>
                    @elseif($item->type === 'file')
                        <p class="mb-0 text-muted">
                            <i class="fas fa-file me-1"></i>
                            {{ $item->file_name }}
                            @if($item->file_size_human)
                                <small>({{ $item->file_size_human }})</small>
                            @endif
                        </p>
                    @else
                        <p class="mb-0 text-muted">
                            <i class="fas fa-external-link-alt me-1"></i>
                            {{ $item->external_url }}
                        </p>
                    @endif
                </div>

                <div class="flex-shrink-0">
                    @if($item->type === 'external')
                        <a href="{{ route('cms.content', [$path, $item->slug]) }}"
                           class="btn btn-outline-primary btn-sm" target="_blank">
                            Open <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    @elseif($item->type === 'file')
                        <a href="{{ route('cms.content', [$path, $item->slug]) }}"
                           class="btn btn-outline-success btn-sm">
                            Download <i class="fas fa-download ms-1"></i>
                        </a>
                    @else
                        <a href="{{ route('cms.content', [$path, $item->slug]) }}"
                           class="btn btn-outline-primary btn-sm">
                            Read more
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-light border">
            No content found{{ $search ? ' for "'.$search.'"' : '' }}.
        </div>
    @endforelse

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $contents->withQueryString()->links() }}
    </div>

</div>
@endsection
