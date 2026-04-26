@extends('frontend.layouts.app')

@section('title', $menuTitle ?? 'Content')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1>{{ $menuTitle }}</h1>
        <p class="text-muted">{{ $contents->total() }} item(s)</p>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search content...">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-control">
                <option value="">All types</option>
                <option value="content" @selected(request('type') === 'content')>Article / Text</option>
                <option value="file" @selected(request('type') === 'file')>File</option>
                <option value="external" @selected(request('type') === 'external')>External Link</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    @forelse($contents as $item)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <h5 class="mb-1">{{ $item->title }}</h5>
                        <p class="mb-2 text-muted small">
                            {{ ucfirst($item->type) }}
                            @if($item->published_at)
                                · {{ $item->published_at->format('d M Y') }}
                            @endif
                        </p>
                        @if($item->type === 'content')
                            <p class="mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($item->body), 200) }}</p>
                        @elseif($item->type === 'file')
                            <p class="mb-0">{{ $item->file_name }} @if($item->file_size_human) ({{ $item->file_size_human }}) @endif</p>
                        @else
                            <p class="mb-0">{{ $item->external_url }}</p>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('cms.item', [$menuPath, $item->slug]) }}" class="btn btn-outline-primary btn-sm">Open</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary">No content found.</div>
    @endforelse

    {{ $contents->links() }}
</div>
@endsection
