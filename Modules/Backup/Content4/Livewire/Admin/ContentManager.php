<?php

namespace Modules\Content\Livewire\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\Content\Models\Content;
use Modules\Menu\Models\Menu;
use Modules\Menu\Models\MenuItem;

class ContentManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    // ── Filters ──────────────────────────────────────────────────────────────
    public string $search    = '';
    public string $filterMenu     = '';
    public string $filterMenuItem = '';
    public string $filterStatus   = '';
    public string $filterType     = '';

    // ── Form state ───────────────────────────────────────────────────────────
    public bool   $showModal  = false;
    public bool   $editMode   = false;
    public ?int   $editingId  = null;

    public int    $menu_id       = 0;
    public int    $menu_item_id  = 0;   // for display/reference only (not stored in contents)
    public string $title         = '';
    public string $slug          = '';
    public string $type          = 'content';
    public string $body          = '';
    public string $external_url  = '';
    public string $status        = 'draft';
    public int    $sort_order    = 0;
    public string $published_at  = '';
    public string $meta_title    = '';
    public string $meta_description = '';
    public string $meta_keywords    = '';
    public        $file          = null;   // uploaded file
    public ?string $existingFile  = null;  // current file_path when editing

    // ── Delete confirm ───────────────────────────────────────────────────────
    public bool  $showDeleteModal = false;
    public ?int  $deletingId      = null;

    // ── Listeners ────────────────────────────────────────────────────────────
    protected $listeners = ['refreshList' => '$refresh'];

    // ── Query string sync ────────────────────────────────────────────────────
    protected $queryString = [
        'search'       => ['except' => ''],
        'filterMenu'   => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterType'   => ['except' => ''],
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Lifecycle
    // ─────────────────────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMenu(): void
    {
        $this->filterMenuItem = '';
        $this->resetPage();
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->editMode || blank($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────────────────

    public function render()
    {
        $query = Content::with(['menu'])
            ->when($this->search, fn ($q) =>
                $q->where(fn ($s) =>
                    $s->where('title', 'like', "%{$this->search}%")
                      ->orWhere('body',  'like', "%{$this->search}%")
                )
            )
            ->when($this->filterMenu,   fn ($q) => $q->where('menu_id', $this->filterMenu))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType,   fn ($q) => $q->where('type', $this->filterType))
            ->orderBy('menu_id')
            ->orderBy('sort_order')
            ->orderByDesc('id');

        $contents  = $query->paginate(15);
        $menus     = Menu::where('is_active', true)->orderBy('name')->get();

        // Menu items for the selected menu (for filter dropdown)
        $menuItems = $this->filterMenu
            ? MenuItem::where('menu_id', $this->filterMenu)
                      ->where('is_active', true)
                      ->orderBy('sort_order')
                      ->get()
            : collect();

        // Menu items for the form modal
        $formMenuItems = $this->menu_id
            ? MenuItem::where('menu_id', $this->menu_id)
                      ->where('is_active', true)
                      ->orderBy('sort_order')
                      ->get()
            : collect();

        return view('content::livewire.admin.content-manager', compact(
            'contents', 'menus', 'menuItems', 'formMenuItems'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD — Create
    // ─────────────────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editMode  = false;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validateForm();

        $data = [
            'menu_id'          => $this->menu_id,
            'title'            => $this->title,
            'slug'             => $this->slug ?: Str::slug($this->title),
            'type'             => $this->type,
            'body'             => $this->type === 'content'  ? $this->body         : null,
            'external_url'     => $this->type === 'external' ? $this->external_url : null,
            'status'           => $this->status,
            'sort_order'       => $this->sort_order,
            'published_at'     => $this->published_at ?: null,
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords'    => $this->meta_keywords,
        ];

        if ($this->type === 'file' && $this->file) {
            // Delete old file when replacing
            if ($this->editMode && $this->existingFile) {
                Storage::disk('public')->delete($this->existingFile);
            }
            $stored = $this->file->store('content-files', 'public');
            $data['file_path'] = $stored;
            $data['file_name'] = $this->file->getClientOriginalName();
            $data['file_size'] = $this->file->getSize();
            $data['file_mime'] = $this->file->getMimeType();
        }

        if ($this->editMode && $this->editingId) {
            $content = Content::findOrFail($this->editingId);
            $content->update($data);
            session()->flash('success', 'Content updated successfully.');
        } else {
            Content::create($data);
            session()->flash('success', 'Content created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD — Edit
    // ─────────────────────────────────────────────────────────────────────────

    public function openEdit(int $id): void
    {
        $content = Content::findOrFail($id);

        $this->editMode   = true;
        $this->editingId  = $id;
        $this->menu_id    = $content->menu_id;
        $this->title      = $content->title;
        $this->slug       = $content->slug;
        $this->type       = $content->type;
        $this->body       = $content->body ?? '';
        $this->external_url  = $content->external_url ?? '';
        $this->status        = $content->status;
        $this->sort_order    = $content->sort_order;
        $this->published_at  = $content->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->meta_title    = $content->meta_title ?? '';
        $this->meta_description = $content->meta_description ?? '';
        $this->meta_keywords    = $content->meta_keywords ?? '';
        $this->existingFile     = $content->file_path;
        $this->file             = null;
        $this->showModal        = true;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD — Delete
    // ─────────────────────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function destroy(): void
    {
        if ($this->deletingId) {
            $content = Content::findOrFail($this->deletingId);
            $content->deleted_by = auth()->id();
            $content->save();
            $content->delete();
            session()->flash('success', 'Content deleted.');
        }

        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId       = null;
        $this->editMode        = false;
        $this->menu_id         = 0;
        $this->menu_item_id    = 0;
        $this->title           = '';
        $this->slug            = '';
        $this->type            = 'content';
        $this->body            = '';
        $this->external_url    = '';
        $this->status          = 'draft';
        $this->sort_order      = 0;
        $this->published_at    = '';
        $this->meta_title      = '';
        $this->meta_description = '';
        $this->meta_keywords   = '';
        $this->file            = null;
        $this->existingFile    = null;
        $this->resetValidation();
    }

    private function validateForm(): void
    {
        $uniqueRule = 'unique:contents,slug,NULL,id,menu_id,' . $this->menu_id;
        if ($this->editMode && $this->editingId) {
            $uniqueRule = "unique:contents,slug,{$this->editingId},id,menu_id,{$this->menu_id}";
        }

        $rules = [
            'menu_id'    => 'required|integer|min:1|exists:menus,id',
            'title'      => 'required|string|max:300',
            'slug'       => ['required', 'string', 'max:300', $uniqueRule],
            'type'       => 'required|in:content,file,external',
            'status'     => 'required|in:draft,published,archived',
            'sort_order' => 'nullable|integer|min:0',
            'published_at' => 'nullable|date',
        ];

        if ($this->type === 'content') {
            $rules['body'] = 'required|string';
        }

        if ($this->type === 'external') {
            $rules['external_url'] = 'required|url';
        }

        if ($this->type === 'file') {
            if (! $this->editMode || $this->file) {
                $rules['file'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:' . config('content.max_file_size_kb', 10240);
            }
        }

        $this->validate($rules);
    }
}
