@extends('backend.layouts.app')

@section('title', 'Create Content')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-plus me-2"></i> Create sdaContent</h4>
        <a href="{{ route('backend.contents.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to list
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @include('content::backend.contents.form', [
                'content' => new \Modules\Content\Models\Content,
                'action'  => route('backend.contents.store'),
                'method'  => 'POST',
            ])
        </div>
    </div>

</div>
@endsection
