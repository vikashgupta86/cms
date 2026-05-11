<div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" @click="show=false"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fa-regular fa-file-lines me-2"></i> Content Management</h4>
        @can('content.create')
            <button wire:click="openCreate" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Content
            </button>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input wire:model.live.debounce.350ms="search"
                               type="text" class="form-control" placeholder="Search title or content…">
                        @if($search)
                            <button wire:click="$set('search','')" class="btn btn-outline-secondary">×</button>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterItem" class="form-select form-select-sm">
                        <option value="">All Menu Items</option>
                        @foreach($flatItems as $fi)
                            <option value="{{ $fi['id'] }}">{{ $fi['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterType" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="content">Content</option>
                        <option value="file">File</option>
                        <option value="external">External</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button wire:click="$set('search','');$set('filterItem','');$set('filterStatus','');$set('filterType','')"
                            class="btn btn-sm btn-outline-secondary w-100" title="Reset filters">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0"
             wire:loading.class="opacity-50"
             wire:target="search,filterItem,filterStatus,filterType">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Title</th>
                        <th>Menu Path</th>
                        <th style="width:90px">Type</th>
                        <th style="width:100px">Status</th>
                        <th style="width:100px">Published</th>
                        <th class="text-end" style="width:90px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contents as $item)
                        <tr>
                            <td class="text-muted small">{{ $item->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $item->title }}</div>
                                <small class="text-muted font-monospace">{{ $item->slug }}</small>
                            </td>
                            <td>
                                @if($item->menuItem)
                                    <small class="text-muted">
                                        {{-- Show breadcrumb path --}}
                                        @php
                                            $crumbs = collect();
                                            $node = $item->menuItem;
                                            while ($node) {
                                                $crumbs->prepend($node->name);
                                                $node = $node->parent_id
                                                    ? \Modules\Menu\Models\MenuItem::find($node->parent_id)
                                                    : null;
                                            }
                                        @endphp
                                        {{ $crumbs->implode(' › ') }}
                                    </small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="{{ $item->type_badge_class }}">{{ ucfirst($item->type) }}</span></td>
                            <td><span class="{{ $item->status_badge_class }}">{{ ucfirst($item->status) }}</span></td>
                            <td><small>{{ $item->published_at?->format('d M Y') ?? '—' }}</small></td>
                            <td class="text-end">
                                @can('content.edit')
                                    <button wire:click="openEdit({{ $item->id }})"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                @endcan
                                @can('content.delete')
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                            class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x d-block mb-2 opacity-25"></i>
                                No content found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-2">
            <small class="text-muted">{{ $contents->total() }} record(s)</small>
            {{ $contents->links() }}
        </div>
    </div>

    {{-- ═══════════════════ CREATE / EDIT MODAL ═══════════════════ --}}
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-semibold">
                        <i class="fas fa-{{ $editMode ? 'pen' : 'plus' }} me-2"></i>
                        {{ $editMode ? 'Edit Content' : 'Add New Content' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Menu Item tree selector --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Menu Section <span class="text-danger">*</span>
                            </label>
                            <select wire:model.live="menu_item_id"
                                    class="form-select @error('menu_item_id') is-invalid @enderror"
                                    style="font-family: monospace">
                                <option value="0">— Select section —</option>
                                @foreach($flatItems as $fi)
                                    <option value="{{ $fi['id'] }}">{{ $fi['label'] }}</option>
                                @endforeach
                            </select>
                            @error('menu_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

                            {{-- Show full path preview --}}
                            @if($menu_item_id)
                                @php
                                    $selItem = \Modules\Menu\Models\MenuItem::find($menu_item_id);
                                    $breadcrumb = collect();
                                    $n = $selItem;
                                    while ($n) {
                                        $breadcrumb->prepend($n->name);
                                        $n = $n->parent_id ? \Modules\Menu\Models\MenuItem::find($n->parent_id) : null;
                                    }
                                @endphp
                                <div class="mt-1 text-muted small">
                                    <i class="fas fa-sitemap me-1"></i>
                                    Path: <strong>{{ $breadcrumb->implode(' › ') }}</strong>
                                </div>
                            @endif
                        </div>

                        {{-- Title --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input wire:model.live="title" type="text" maxlength="300"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="Enter content title">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Slug (auto-generated, but editable) --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">URL Slug</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted" style="font-size:.8rem">/</span>
                                <input wire:model="slug" type="text" maxlength="500"
                                       class="form-control font-monospace @error('slug') is-invalid @enderror"
                                       placeholder="auto-generated from menu path + title"
                                       style="font-size:.85rem">
                            </div>
                            @error('slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <small class="text-muted">
                                Auto-generated as <code>menu-path/title-slug</code>. Edit only if needed.
                            </small>
                        </div>

                        {{-- Type --}}
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Content Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2 mt-1">
                                <label class="flex-fill text-center">
                                    <input wire:model.live="type" type="radio" value="content" class="btn-check">
                                    <span class="btn btn-outline-primary w-100 btn-sm">
                                        <i class="fas fa-align-left d-block mb-1"></i> Content
                                    </span>
                                </label>
                                <label class="flex-fill text-center">
                                    <input wire:model.live="type" type="radio" value="file" class="btn-check">
                                    <span class="btn btn-outline-info w-100 btn-sm">
                                        <i class="fas fa-file-alt d-block mb-1"></i> File
                                    </span>
                                </label>
                                <label class="flex-fill text-center">
                                    <input wire:model.live="type" type="radio" value="external" class="btn-check">
                                    <span class="btn btn-outline-warning w-100 btn-sm">
                                        <i class="fas fa-external-link-alt d-block mb-1"></i> Link
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select wire:model="status"
                                    class="form-select @error('status') is-invalid @enderror">
                                <option value="draft">📝 Draft</option>
                                <option value="published">✅ Published</option>
                                <option value="archived">📦 Archived</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Sort order --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input wire:model="sort_order" type="number" min="0" class="form-control">
                        </div>

                        {{-- Publish date --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Publish Date</label>
                            <input wire:model="published_at" type="datetime-local"
                                   class="form-control @error('published_at') is-invalid @enderror"
                                   style="max-width:280px">
                            @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Content Editor --}}
                        @if($type === 'content')
                        <div class="col-12">
                            <label class="form-label fw-semibold">Body <span class="text-danger">*</span></label>
                            <div class="mb-2">
                                <small class="text-muted">Rich-text editor — HTML is fully supported. Use the toolbar above to format content.</small>
                            </div>
                            <textarea wire:model.defer="body" wire:ignore id="summernote-editor" rows="12"
                                      class="form-control @error('body') is-invalid @enderror"
                                      placeholder="Enter HTML content…">{{ $body }}</textarea>
                            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @endif

                        {{-- File Upload --}}
                        @if($type === 'file')
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Upload File @if(!$editMode)<span class="text-danger">*</span>@endif
                            </label>
                            @if($existingFileName)
                                <div class="alert alert-info py-2 mb-2 small">
                                    <i class="fas fa-file me-1"></i>
                                    Current: <strong>{{ $existingFileName }}</strong>
                                    — upload a new file to replace it
                                </div>
                            @endif
                            <input wire:model="file" type="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div wire:loading wire:target="file" class="text-info small mt-1">
                                <i class="fas fa-spinner fa-spin me-1"></i> Uploading…
                            </div>
                            <small class="text-muted">
                                PDF, DOC, DOCX, XLS, XLSX, JPG, PNG —
                                max {{ number_format(config('content.max_file_size_kb', 10240) / 1024, 0) }} MB
                            </small>
                        </div>
                        @endif

                        {{-- External URL --}}
                        @if($type === 'external')
                        <div class="col-12">
                            <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <input wire:model="external_url" type="url"
                                       class="form-control @error('external_url') is-invalid @enderror"
                                       placeholder="https://example.com">
                            </div>
                            @error('external_url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        @endif

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" wire:click="save"
                            wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading wire:target="save">
                            <i class="fas fa-spinner fa-spin me-1"></i> Saving…
                        </span>
                        <span wire:loading.remove wire:target="save">
                            <i class="fas fa-check me-1"></i> {{ $editMode ? 'Update' : 'Save' }}
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════ DELETE MODAL ═══════════════════ --}}
    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt fa-2x text-danger mb-3 d-block"></i>
                    <h6>Delete this content?</h6>
                    <p class="text-muted small mb-0">It will be soft-deleted and can be restored.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button class="btn btn-light btn-sm px-4" wire:click="cancelDelete">Cancel</button>
                    <button class="btn btn-danger btn-sm px-4" wire:click="destroy">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>



@push("after-styles")
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet" />
    <style>
        .note-editor.note-frame :after {
            display: none;
        }

        .note-editor .note-toolbar .note-dropdown-menu,
        .note-popover .popover-content .note-dropdown-menu {
            min-width: 180px;
        }
    </style>
@endpush

@push("after-scripts")
    <script
        type="module"
        src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"
    ></script>
    <script type="module">
        // Define function to open filemanager window
        var lfm = function (options, cb) {
            var route_prefix = options && options.prefix ? options.prefix : '/laravel-filemanager';
            window.open(route_prefix + '?type=' + options.type || 'file', 'FileManager', 'width=900,height=600');
            window.SetUrl = cb;
        };

        // Define LFM summernote button
        var LFMButton = function (context) {
            var ui = $.summernote.ui;
            var button = ui.button({
                contents: '<i class="note-icon-picture"></i> ',
                tooltip: 'Insert image with filemanager',
                click: function () {
                    lfm(
                        {
                            type: 'image',
                            prefix: '/laravel-filemanager',
                        },
                        function (lfmItems, path) {
                            lfmItems.forEach(function (lfmItem) {
                                context.invoke('insertImage', lfmItem.url);
                            });
                        },
                    );
                },
            });
            return button.render();
        };

        // Initialize Summernote editor
        function initSummernote() {
            const $editor = $('#summernote-editor');

            // Destroy existing instance if it exists
            if ($editor.data('summernote')) {
                $editor.summernote('destroy');
            }

            // Initialize Summernote
            $editor.summernote({
                height: 300,
                minHeight: 200,
                maxHeight: 500,
                placeholder: 'Enter HTML content…',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['fontname', 'fontsize', 'bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['color', ['forecolor', 'backcolor']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'lfm', 'video']],
                    ['view', ['fullscreen', 'codeview', 'undo', 'redo', 'help']],
                ],
                buttons: {
                    lfm: LFMButton,
                },
                callbacks: {
                    onChange: function(contents) {
                        // Update Livewire model with editor content
                        @this.set('body', contents);
                    },
                    onImageUpload: function(files) {
                        // Handle image upload if needed
                        for (let i = 0; i < files.length; i++) {
                            sendFile(files[i], this);
                        }
                    }
                }
            });

            // Set initial content
            $editor.summernote('code', $editor.val());
        }

        // Initialize on document ready
        $(document).ready(function() {
            initSummernote();

            // Reinitialize when modal is shown (for Livewire compatibility)
            $(document).on('shown.bs.modal', '.modal', function() {
                if ($('#summernote-editor').length) {
                    setTimeout(initSummernote, 100);
                }
            });
        });

        // Handle Livewire updates
        document.addEventListener('livewire:updated', function() {
            if ($('#summernote-editor').length) {
                initSummernote();
            }
        });
    </script>

    <script type="module" src="{{ asset("vendor/laravel-filemanager/js/stand-alone-button.js") }}"></script>
    <script type="module">
        $('#button-image').filemanager('image');
    </script>

    <!-- Select2 Library -->
    <x-library.select2 />
    <script type="module">
        $(document).ready(function () {
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
                document.querySelector('.select2-container--open .select2-search__field').focus();
            });

            $('.select2-category').select2({
                theme: 'bootstrap-5',
                placeholder: '@lang("Select an option")',
                minimumInputLength: 2,
                allowClear: true,
                ajax: {
                    url: '{{ route("backend.categories.index_list") }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term),
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data,
                        };
                    },
                    cache: true,
                },
            });

            $('.select2-tags').select2({
                theme: 'bootstrap-5',
                placeholder: '@lang("Select an option")',
                minimumInputLength: 2,
                allowClear: true,
                ajax: {
                    url: '{{ route("backend.tags.index_list") }}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term),
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data,
                        };
                    },
                    cache: true,
                },
            });
        });
    </script>
@endpush