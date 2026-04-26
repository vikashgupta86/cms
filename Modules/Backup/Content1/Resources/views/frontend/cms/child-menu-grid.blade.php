@extends('frontend.layouts.app')

@section('title', $menu->name ?? 'Menu')

@section('content')
<div class="container py-5">

    <div class="mb-5">
        <h1 class="h3">{{ $menu->name }}</h1>
        @if($menu->description)
            <p class="text-muted">{{ $menu->description }}</p>
        @endif
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($children as $child)
            <div class="col">
                <a href="{{ url($child->full_slug ?? $child->slug) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 hover-shadow">
                        <div class="card-body">
                            <h5 class="card-title text-dark">{{ $child->name }}</h5>
                            @if($child->description)
                                <p class="card-text text-muted small">
                                    {{ \Illuminate\Support\Str::limit($child->description, 120) }}
                                </p>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <span class="btn btn-sm btn-outline-primary">View &rarr;</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border">No sub-sections found.</div>
            </div>
        @endforelse
    </div>

</div>
@endsection
