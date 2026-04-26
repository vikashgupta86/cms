@extends("backend.layouts.app")

@section("title")
    @lang("Dashboard")
@endsection

@section("breadcrumbs")
    <x-backend.breadcrumbs />
@endsection

@section("content")
    <div class="card mb-4">
        <div class="card-body">
            <x-backend.section-header>
                @lang("Admin Dashboard")

                <x-slot name="toolbar">
                    <a href="{{ route('backend.backups.backup') }}" class="btn btn-outline-primary mb-1">
                        <i class="fa-solid fa-database"></i>
                        @lang('Create Backup')
                    </a>
                </x-slot>
            </x-backend.section-header>

            <!-- Dashboard Content Area -->

            <!-- / Dashboard Content Area -->
        </div>
    </div>

    {{-- Demo content --}}
    @include("backend.includes.dashboard_demo_data")
@endsection
