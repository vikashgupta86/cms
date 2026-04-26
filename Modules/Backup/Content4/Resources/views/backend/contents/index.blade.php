@extends('backend.layouts.app')

@section('title', 'Content Management')

@section('content')
<div class="container-fluid py-3">
    @livewire('content-manager')
</div>
@endsection
