@extends('backend.layouts.app')

@section('title') {{ __($module_action) }} {{ __($module_title) }} @endsection

@section('breadcrumbs')
    <x-backend.breadcrumbs>
        <x-backend.breadcrumb-item route='{{route("backend.$module_name.index")}}' icon='{{ $module_icon }}'>
            {{ __($module_title) }}
        </x-backend.breadcrumb-item>
        <x-backend.breadcrumb-item type="active">{{ __($module_action) }}</x-backend.breadcrumb-item>
    </x-backend.breadcrumbs>
@endsection
 

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h4 class="card-title mb-0">
                        <i class="{{ $module_icon }}"></i> {{ __($module_title) }} "{{ ${$module_name_singular}->name }}"
                        <small class="text-muted">{{ __($module_action) }}</small>
                    </h4>
                    <div class="small text-muted">
                        {{ __('menu::text.updated_at') }} {{ ${$module_name_singular}->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>

            <hr>

            <div class="row mt-4">
                <div class="col-12">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> {{ __('menu::text.menu_items') }}
                            <span class="badge bg-primary">{{ ${$module_name_singular}->allItems->count() }}</span>
                        </h5>
                        <x-backend.buttons.create
                            route="{{ route('backend.menuitems.create', ['menu_id' => ${$module_name_singular}->id]) }}"
                            title="{{ __('menu::text.add_item') }}"
                            icon="fas fa-plus-circle"
                            small="true" />
                    </div>

                    @if(${$module_name_singular}->items->count() > 0)

                        {{-- Search & Filter Row --}}
                    
  <div class="row mb-3 g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="menuSearch" class="form-control" placeholder="Search menu items...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">Type</label>
                                <select id="typeFilter" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="link">Link</option>
                                    <option value="dropdown">Dropdown</option>
                                    <option value="content">Content</option>
                                    <option value="file">File</option>
                                    <option value="external">External</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">Status</label>
                                <select id="statusFilter" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="visible">Visible</option>
                                    <option value="hidden">Hidden</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-muted mb-1">&nbsp;</label>
                                <button type="button" id="resetFilter" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            
                            <table class="table table-bordered table-hover" id="menuItemsTable">
                      
                                <thead class="table-light">
                                    <tr>
                                        {{-- col 0 --}}
                                        <th width="60">{{ __('menu::text.order') }}</th>
                                        {{-- col 1 --}}
                                        <th>{{ __('menu::text.name') }}</th>
                                        {{-- col 2 --}}
                                        <th width="100">{{ __('menu::text.type') }}</th>
                                        {{-- col 3 --}}
                                        <th width="180">Section Type</th>
                                        {{-- col 4 --}}
                                        <th width="120">Section Order</th>
                                        {{-- col 5 --}}
                                        <th width="130">{{ __('menu::text.status') }}</th>
                                        {{-- col 6 --}}
                                        <th class="text-center no-sort" width="110">{{ __('menu::text.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(${$module_name_singular}->items->sortBy('sort_order') as $item)
                                        @include('managehomepage::backend.managehomepages.partials.menu-item-row', ['item' => $item, 'level' => 0])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            {{ __('menu::text.no_menu_items') }}
                            <a href="{{ route('backend.menuitems.create', ['menu_id' => ${$module_name_singular}->id]) }}"
                                class="alert-link">
                                {{ __('menu::text.add_first_item') }}
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    <small class="text-muted">
                        <strong>{{ __('menu::text.created_at') }}:</strong> {{ ${$module_name_singular}->created_at }}
                        ({{ ${$module_name_singular}->created_at->diffForHumans() }}),
                        <strong>{{ __('menu::text.updated_at') }}:</strong> {{ ${$module_name_singular}->updated_at }}
                        ({{ ${$module_name_singular}->updated_at->diffForHumans() }})
                        @if(${$module_name_singular}->deleted_at)
                            <strong>{{ __('menu::text.deleted_at') }}:</strong> {{ ${$module_name_singular}->deleted_at }}
                            ({{ ${$module_name_singular}->deleted_at->diffForHumans() }})
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection
<script type="module" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
@push ('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush
@push('after-scripts')
  

    <script>
     (function ($) {
        $(document).ready(function () {

            var $table = $('#menuItemsTable');
            if ($table.length === 0) return;

            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
            }

            var table = $table.DataTable({
                responsive:  true,
                pageLength:  25,
                lengthMenu:  [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                ordering:    true,
                searching:   true,
                paging:      true,
                info:        true,
                autoWidth:   false,
             
                columnDefs: [
                    {
                        // col 0: Menu Order — sortable, read plain text
                        targets:    0,
                        orderable:  true,
                        searchable: true,
                        type:       'num'  // treat as number so 2 < 10 (not string sort)
                    },
                    {
                        // col 3: Section Type <select> — can't sort/search a dropdown
                        targets:    3,
                        orderable:  false,
                        searchable: false
                    },
                    {
                        // col 4: Section Order <input type="number">
                        // Read the input's value attribute for sorting
                        targets: 4,
                        orderable:  true,
                        searchable: false,
                        type:       'num',
                        render: function (data, type, row, meta) {
                            if (type === 'sort' || type === 'type') {
                                // Extract the value="..." from the <input> HTML
                                var match = data.match(/value="([^"]*)"/);
                                return match ? parseFloat(match[1]) || 0 : 0;
                            }
                            return data; // display mode: return raw HTML as-is
                        }
                    },
                    {
                        // col 6: Actions — no sort, no search
                        targets:    'no-sort',
                        orderable:  false,
                        searchable: false
                    }
                ],
                language: {
                    lengthMenu:  'Show _MENU_ entries',
                    info:        'Showing _START_ to _END_ of _TOTAL_ items',
                    infoEmpty:   'No items found',
                    emptyTable:  'No menu items available',
                    zeroRecords: 'No items match your search'
                },
                drawCallback: function () {
                    $table.find('tbody tr').each(function () {
                        var lvl = parseInt($(this).data('level')) || 0;
                        $(this)
                            .removeClass('menu-item-level-0 menu-item-level-1 menu-item-level-2 menu-item-level-3')
                            .addClass('menu-item-level-' + lvl);
                    });
                }
            });

            // Global search — searches col 0 (order), 1 (name), 2 (type), 5 (status)
            $('#menuSearch').on('keyup input', function () {
                table.search($(this).val()).draw();
            });

            // Type column filter — col 2
            $('#typeFilter').on('change', function () {
                table.column(2).search($(this).val()).draw();
            });

            // Status column filter — col 5
            $('#statusFilter').on('change', function () {
                table.column(5).search($(this).val()).draw();
            });

            // Reset all filters
            $('#resetFilter').on('click', function () {
                $('#menuSearch').val('');
                $('#typeFilter').val('');
                $('#statusFilter').val('');
                table.search('').columns().search('').draw();
            });

        });
    }(jQuery));

        // Save Placement AJAX
        $(document).on('click', '.save-placement-btn', function () {
            var button      = $(this);
            var id          = button.data('id');
            var sectionType = $('.section-type[data-id="' + id + '"]').val();
            var sortOrder   = $('.menu-position[data-id="' + id + '"]').val();
            var messageBox  = $('.save-message-' + id);

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            messageBox.removeClass('text-success text-danger').text('');

            $.ajax({
                url:  "{{ url('admin/managehomepages') }}/" + id + "/placement",
                type: 'POST',
                data: {
                    _token:       "{{ csrf_token() }}",
                    menu_item_id: id,
                    section_type: sectionType,
                    sort_order:   sortOrder
                },
                success: function (response) {
                    messageBox.addClass('text-success').text(response.message || 'Saved successfully');
                    button.html('<i class="fas fa-check"></i>');
                    setTimeout(function () {
                        button.html('<i class="fas fa-save"></i>');
                        messageBox.text('');
                    }, 1500);
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Something went wrong';
                    messageBox.addClass('text-danger').text(msg);
                    button.html('<i class="fas fa-save"></i>');
                },
                complete: function () {
                    button.prop('disabled', false);
                }
            });
        });
    </script>
@endpush