@extends('backend.layouts.app')

@section('title', 'Contents')

@section('content')
<div class="container-fluid py-3">

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fa-regular fa-file-lines me-2"></i> Contents</h4>
        @can('content.create')
            <a href="{{ route('backend.contents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Content
            </a>
        @endcan
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('backend.contents.index') }}" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Search title or body…">
                </div>
                <div class="col-md-3">
                    <select name="menu_id" class="form-select">
                        <option value="">All Menus</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" @selected(request('menu_id') == $menu->id)>
                                {{ $menu->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft"     @selected(request('status') === 'draft')>Draft</option>
                        <option value="published" @selected(request('status') === 'published')>Published</option>
                        <option value="archived"  @selected(request('status') === 'archived')>Archived</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                    <a href="{{ route('backend.contents.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Title</th>
                        <th>Menu</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Sort</th>
                        <th>Published</th>
                        <th class="text-end" style="width:160px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contents as $content)
                        <tr>
                            <td>{{ $content->id }}</td>
                            <td>
                                <strong>{{ $content->title }}</strong>
                                <br><small class="text-muted">{{ $content->slug }}</small>
                            </td>
                            <td>{{ $content->menu?->name ?? '—' }}</td>
                            <td><span class="{{ $content->type_badge_class }}">{{ ucfirst($content->type) }}</span></td>
                            <td><span class="{{ $content->status_badge_class }}">{{ ucfirst($content->status) }}</span></td>
                            <td>{{ $content->sort_order }}</td>
                            <td>{{ $content->published_at?->format('d M Y') ?? '—' }}</td>
                            <td class="text-end">
                                @can('content.edit')
                                    <a href="{{ route('backend.contents.edit', $content) }}"
                                       class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('content.delete')
                                    <form method="POST"
                                          action="{{ route('backend.contents.destroy', $content) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this content?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No content found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">Total: {{ $contents->total() }}</small>
            {{ $contents->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection
