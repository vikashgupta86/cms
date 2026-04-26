<div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fa-regular fa-file-lines me-2"></i> Content Management</h4>
        @can('content.create')
            <button wire:click="openCreate" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Content
            </button>
        @endcan
    </div>

    {{-- ── Filters ─────────────────────────────────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input wire:model.live.debounce.400ms="search"
                           type="text" class="form-control form-control-sm"
                           placeholder="Search title or body…">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterMenu" class="form-select form-select-sm">
                        <option value="">All Menus</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}">
                                {{ $menu->name }} ({{ $menu->location }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
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
                <div class="col-md-2">
                    <button wire:click="$set('search','');$set('filterMenu','');$set('filterStatus','');$set('filterType','')"
                            class="btn btn-sm btn-outline-secondary w-100">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Table ───────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body p-0" wire:loading.class="opacity-50">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Title / Slug</th>
                        <th>Menu</th>
                        <th>Menu Items</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Sort</th>
                        <th>Published</th>
                        <th class="text-end" style="width:110px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contents as $content)
                        <tr>
                            <td class="text-muted small">{{ $content->id }}</td>
                            <td>
                                <strong>{{ $content->title }}</strong>
                                <br><small class="text-muted">{{ $content->slug }}</small>
                            </td>
                            <td>
                                @if($content->menu)
                                    <span class="badge bg-light text-dark border">{{ $content->menu->name }}</span>
                                    <br><small class="text-muted fst-italic">{{ $content->menu->location }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="max-width:200px">
                                @if($content->menu)
                                    @php $items = $content->menu->allItems()->take(4)->get(); @endphp
                                    @foreach($items as $mi)
                                        <span class="badge bg-secondary me-1 mb-1" title="depth: {{ $mi->depth }}">
                                            {{ $mi->name }}
                                        </span>
                                    @endforeach
                                    @php $total = $content->menu->allItems()->count(); @endphp
                                    @if($total > 4)
                                        <br><small class="text-muted">+{{ $total - 4 }} more items</small>
                                    @endif
                                @endif
                            </td>
                            <td><span class="{{ $content->type_badge_class }}">{{ ucfirst($content->type) }}</span></td>
                            <td><span class="{{ $content->status_badge_class }}">{{ ucfirst($content->status) }}</span></td>
                            <td>{{ $content->sort_order }}</td>
                            <td><small>{{ $content->published_at?->format('d M Y') ?? '—' }}</small></td>
                            <td class="text-end">
                                @can('content.edit')
                                    <button wire:click="openEdit({{ $content->id }})" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('content.delete')
                                    <button wire:click="confirmDelete({{ $content->id }})" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No content found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center py-2">
            <small class="text-muted">Total: {{ $contents->total() }}</small>
            {{ $contents->links() }}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         Create / Edit Modal
    ══════════════════════════════════════════════════════════════════════════ --}}
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.55)">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-{{ $editMode ? 'edit' : 'plus' }} me-2"></i>
                        {{ $editMode ? 'Edit Content' : 'New Content' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Menu --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Menu <span class="text-danger">*</span>
                            </label>
                            <select wire:model.live="menu_id"
                                    class="form-select @error('menu_id') is-invalid @enderror">
                                <option value="0">— Select Menu —</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}">
                                        {{ $menu->name }} — {{ $menu->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('menu_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Menu items panel (read-only, shows hierarchy) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Menu Items</label>
                            @if($menu_id && $formMenuItems->isNotEmpty())
                                <div class="border rounded p-2 bg-light" style="max-height:110px;overflow-y:auto;font-size:.82rem">
                                    @foreach($formMenuItems as $mi)
                                        <div class="mb-1">
                                            <span style="padding-left:{{ $mi->depth * 12 }}px">
                                                @if($mi->depth > 0)<span class="text-muted me-1">└</span>@endif
                                                <span class="badge bg-secondary">{{ $mi->name }}</span>
                                                <small class="text-muted">{{ $mi->slug }}</small>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">{{ $formMenuItems->count() }} item(s)</small>
                            @elseif($menu_id)
                                <div class="form-control bg-light text-muted" style="height:auto;min-height:38px">
                                    No menu items found in this menu.
                                </div>
                            @else
                                <div class="form-control bg-light text-muted" style="height:auto;min-height:38px">
                                    Select a menu to preview its items.
                                </div>
                            @endif
                        </div>

                        {{-- Title --}}
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input wire:model.live="title" type="text" maxlength="300"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="Enter content title">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Slug --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Slug</label>
                            <input wire:model="slug" type="text" maxlength="300"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="auto-generated">
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Type --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select wire:model.live="type"
                                    class="form-select @error('type') is-invalid @enderror">
                                <option value="content">Content (HTML)</option>
                                <option value="file">File Download</option>
                                <option value="external">External URL</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select wire:model="status"
                                    class="form-select @error('status') is-invalid @enderror">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Sort --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input wire:model="sort_order" type="number" min="0"
                                   class="form-control @error('sort_order') is-invalid @enderror">
                            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Publish date --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Publish Date</label>
                            <input wire:model="published_at" type="datetime-local"
                                   class="form-control @error('published_at') is-invalid @enderror">
                            @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Body --}}
                        @if($type === 'content')
                        <div class="col-12">
                            <label class="form-label fw-semibold">Body <span class="text-danger">*</span></label>
                            <textarea wire:model="body" rows="10"
                                      class="form-control @error('body') is-invalid @enderror"
                                      placeholder="Enter HTML content…"></textarea>
                            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">HTML is supported.</small>
                        </div>
                        @endif

                        {{-- File --}}
                        @if($type === 'file')
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                File @if(!$editMode)<span class="text-danger">*</span>@endif
                            </label>
                            <input wire:model="file" type="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">
                                Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG.
                                Max: {{ number_format(config('content.max_file_size_kb', 10240) / 1024, 0) }} MB.
                            </small>
                            @if($existingFile)
                                <div class="mt-2 p-2 bg-light border rounded text-success small">
                                    <i class="fas fa-file me-1"></i>
                                    Current: <strong>{{ basename($existingFile) }}</strong>
                                    — upload a new file to replace it.
                                </div>
                            @endif
                            <div wire:loading wire:target="file" class="mt-1 text-info small">
                                <i class="fas fa-spinner fa-spin me-1"></i> Uploading…
                            </div>
                        </div>
                        @endif

                        {{-- External URL --}}
                        @if($type === 'external')
                        <div class="col-12">
                            <label class="form-label fw-semibold">External URL <span class="text-danger">*</span></label>
                            <input wire:model="external_url" type="url"
                                   class="form-control @error('external_url') is-invalid @enderror"
                                   placeholder="https://example.com">
                            @error('external_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @endif

                        {{-- SEO --}}
                        <div class="col-12 mt-1"><hr class="my-1"><p class="text-muted small fw-semibold mb-0">SEO / Meta</p></div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input wire:model="meta_title" type="text" maxlength="300" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input wire:model="meta_keywords" type="text" maxlength="500" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Meta Description</label>
                            <textarea wire:model="meta_description" rows="2" class="form-control"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="save">
                        <span wire:loading wire:target="save">
                            <i class="fas fa-spinner fa-spin me-1"></i>
                        </span>
                        <i class="fas fa-save me-1" wire:loading.remove wire:target="save"></i>
                        {{ $editMode ? 'Update Content' : 'Create Content' }}
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         Delete Confirm Modal
    ══════════════════════════════════════════════════════════════════════════ --}}
    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.55)">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                </div>
                <div class="modal-body pt-1">
                    Are you sure? This content will be soft-deleted and can be restored from the database.
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-outline-secondary btn-sm" wire:click="cancelDelete">Cancel</button>
                    <button class="btn btn-danger btn-sm" wire:click="destroy">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
