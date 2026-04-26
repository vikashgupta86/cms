{{--
    Shared form partial used by both create.blade.php and edit.blade.php.
    Variables available:
      $content  – the Content model instance (may be new/empty on create)
      $menus    – collection of Menu models
      $action   – form action URL
      $method   – 'POST' or 'PUT'
--}}

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="row g-3">

        {{-- Menu --}}
        <div class="col-md-6">
            <label for="menu_id" class="form-label">Menu <span class="text-danger">*</span></label>
            <select name="menu_id" id="menu_id" class="form-select @error('menu_id') is-invalid @enderror" required>
                <option value="">— Select Menu —</option>
                @foreach($menus as $menu)
                    <option value="{{ $menu->id }}"
                        @selected(old('menu_id', $content->menu_id ?? '') == $menu->id)>
                        {{ $menu->name }}
                    </option>
                @endforeach
            </select>
            @error('menu_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Status --}}
        <div class="col-md-3">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="draft"     @selected(old('status', $content->status ?? 'draft') === 'draft')>Draft</option>
                <option value="published" @selected(old('status', $content->status ?? '') === 'published')>Published</option>
                <option value="archived"  @selected(old('status', $content->status ?? '') === 'archived')>Archived</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Sort Order --}}
        <div class="col-md-3">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" min="0"
                   value="{{ old('sort_order', $content->sort_order ?? 0) }}"
                   class="form-control @error('sort_order') is-invalid @enderror">
            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Title --}}
        <div class="col-md-8">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" maxlength="300"
                   value="{{ old('title', $content->title ?? '') }}"
                   class="form-control @error('title') is-invalid @enderror" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Slug --}}
        <div class="col-md-4">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" id="slug" maxlength="300"
                   value="{{ old('slug', $content->slug ?? '') }}"
                   class="form-control @error('slug') is-invalid @enderror"
                   placeholder="Auto-generated from title">
            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Type --}}
        <div class="col-md-4">
            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                <option value="content"  @selected(old('type', $content->type ?? 'content') === 'content')>Content (Text/HTML)</option>
                <option value="file"     @selected(old('type', $content->type ?? '') === 'file')>File Download</option>
                <option value="external" @selected(old('type', $content->type ?? '') === 'external')>External URL</option>
            </select>
            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Published At --}}
        <div class="col-md-4">
            <label for="published_at" class="form-label">Publish Date</label>
            <input type="datetime-local" name="published_at" id="published_at"
                   value="{{ old('published_at', isset($content->published_at) ? $content->published_at?->format('Y-m-d\TH:i') : '') }}"
                   class="form-control @error('published_at') is-invalid @enderror">
            @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Body (shown when type=content) --}}
        <div class="col-12" id="field-body">
            <label for="body" class="form-label">Body <span class="text-danger">*</span></label>
            <textarea name="body" id="body" rows="12"
                      class="form-control @error('body') is-invalid @enderror">{{ old('body', $content->body ?? '') }}</textarea>
            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">HTML is allowed.</small>
        </div>

        {{-- File upload (shown when type=file) --}}
        <div class="col-12" id="field-file" style="display:none">
            <label for="file" class="form-label">
                File <span class="text-danger" id="file-required-star">*</span>
            </label>
            <input type="file" name="file" id="file"
                   class="form-control @error('file') is-invalid @enderror"
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Max: {{ number_format(config('content.max_file_size_kb', 10240) / 1024, 0) }} MB.</small>
            @if(isset($content) && $content->file_path)
                <div class="mt-2">
                    <span class="text-success"><i class="fas fa-file me-1"></i>
                        Current: <strong>{{ $content->file_name }}</strong>
                        @if($content->file_size_human) ({{ $content->file_size_human }}) @endif
                    </span>
                    <br><small class="text-muted">Upload a new file to replace it.</small>
                </div>
            @endif
        </div>

        {{-- External URL (shown when type=external) --}}
        <div class="col-12" id="field-external" style="display:none">
            <label for="external_url" class="form-label">External URL <span class="text-danger">*</span></label>
            <input type="url" name="external_url" id="external_url"
                   value="{{ old('external_url', $content->external_url ?? '') }}"
                   class="form-control @error('external_url') is-invalid @enderror"
                   placeholder="https://example.com">
            @error('external_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Meta fields --}}
        <div class="col-12 mt-2">
            <hr>
            <h6 class="text-muted">SEO / Meta</h6>
        </div>
        <div class="col-md-6">
            <label for="meta_title" class="form-label">Meta Title</label>
            <input type="text" name="meta_title" id="meta_title" maxlength="300"
                   value="{{ old('meta_title', $content->meta_title ?? '') }}"
                   class="form-control @error('meta_title') is-invalid @enderror">
            @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="meta_keywords" class="form-label">Meta Keywords</label>
            <input type="text" name="meta_keywords" id="meta_keywords" maxlength="500"
                   value="{{ old('meta_keywords', $content->meta_keywords ?? '') }}"
                   class="form-control @error('meta_keywords') is-invalid @enderror">
            @error('meta_keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label for="meta_description" class="form-label">Meta Description</label>
            <textarea name="meta_description" id="meta_description" rows="2"
                      class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $content->meta_description ?? '') }}</textarea>
            @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Submit --}}
        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save Content
            </button>
            <a href="{{ route('backend.contents.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>

    </div>{{-- .row --}}
</form>

@push('scripts')
<script>
(function () {
    const typeSelect  = document.getElementById('type');
    const fieldBody   = document.getElementById('field-body');
    const fieldFile   = document.getElementById('field-file');
    const fieldExt    = document.getElementById('field-external');

    function toggleFields() {
        const val = typeSelect.value;
        fieldBody.style.display = val === 'content'  ? '' : 'none';
        fieldFile.style.display = val === 'file'     ? '' : 'none';
        fieldExt.style.display  = val === 'external' ? '' : 'none';
    }

    typeSelect.addEventListener('change', toggleFields);
    toggleFields(); // run on page load
})();
</script>
@endpush
