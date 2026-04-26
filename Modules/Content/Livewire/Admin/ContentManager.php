<?php

namespace Modules\Content\Livewire\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\Content\Models\Content;
use Modules\Menu\Models\MenuItem;

class ContentManager extends Component
{
    use WithPagination, WithFileUploads;

    // ── Filters ───────────────────────────────────────────────────────────
    public string $search       = '';
    public string $filterItem   = '';
    public string $filterStatus = '';
    public string $filterType   = '';

    // ── Form fields ───────────────────────────────────────────────────────
    public bool    $showModal  = false;
    public bool    $editMode   = false;
    public ?int    $editingId  = null;

    public int     $menu_item_id     = 0;
    public string  $title            = '';
    public string  $slug             = '';
    public string  $type             = 'content';
    public string  $body             = '';
    public string  $external_url     = '';
    public string  $status           = 'draft';
    public int     $sort_order       = 0;
    public string  $published_at     = '';
    public         $file             = null;
    public ?string $existingFile     = null;
    public ?string $existingFileName = null;

    // ── Delete ────────────────────────────────────────────────────────────
    public bool $showDeleteModal = false;
    public ?int $deletingId      = null;

    protected $queryString = [
        'search'       => ['except' => ''],
        'filterItem'   => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    // ── Lifecycle ─────────────────────────────────────────────────────────

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingFilterItem(): void { $this->resetPage(); }

    public function updatedTitle(string $v): void
    {
        if (! $this->editMode) {
            $this->slug = $this->buildFullSlug($this->menu_item_id, $v);
        }
    }

    public function updatedMenuItemId(int $v): void
    {
        // Rebuild slug when menu item changes (only on create)
        if (! $this->editMode && $this->title) {
            $this->slug = $this->buildFullSlug($v, $this->title);
        }
    }

    // ── Build hierarchical slug: home/about-us/title-slug ────────────────

    private function buildFullSlug(int $menuItemId, string $title): string
    {
        if (! $menuItemId) {
            return Str::slug($title);
        }

        $segments = $this->getAncestorSlugs($menuItemId);
        $segments[] = Str::slug($title);

        return implode('/', array_filter($segments));
    }

    private function getAncestorSlugs(int $menuItemId): array
    {
        // Load all menu items once keyed by id
        $all = MenuItem::whereNotNull('slug')
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $item = $all->get($menuItemId);
        if (! $item) return [];

        $chain = [];
        $current = $item;

        // Walk up the parent chain
        while ($current) {
            array_unshift($chain, Str::slug($current->slug ?? $current->name));
            $current = $current->parent_id ? $all->get($current->parent_id) : null;
        }

        return $chain;
    }

    // ── Build tree for dropdown ───────────────────────────────────────────

    #[Computed]
    public function menuItemTree(): array
    {
        $all = MenuItem::where('is_active', true)
            ->orderBy('menu_id')
            ->orderBy('sort_order')
            ->get();

        // Index by id
        $indexed = $all->keyBy('id');

        // Build tree recursively
        $result = [];
        foreach ($all as $item) {
            if (is_null($item->parent_id)) {
                $result[] = $this->buildTreeNode($item, $indexed, 0);
            }
        }

        return $result;
    }

    private function buildTreeNode($item, $indexed, int $depth): array
    {
        $children = $indexed->filter(fn ($i) => $i->parent_id === $item->id)
            ->sortBy('sort_order');

        $node = [
            'id'       => $item->id,
            'name'     => $item->name,
            'depth'    => $depth,
            'children' => [],
            'fullSlug' => implode('/', $this->getAncestorSlugs($item->id)),
        ];

        foreach ($children as $child) {
            $node['children'][] = $this->buildTreeNode($child, $indexed, $depth + 1);
        }

        return $node;
    }

    // Flatten tree into ordered list for <select> with visual indentation
    private function flattenTree(array $nodes): array
    {
        $flat = [];
        foreach ($nodes as $node) {
            $flat[] = [
                'id'       => $node['id'],
                'label'    => str_repeat('　', $node['depth']) . ($node['depth'] > 0 ? '└ ' : '') . $node['name'],
                'fullSlug' => $node['fullSlug'],
            ];
            if (! empty($node['children'])) {
                foreach ($this->flattenTree($node['children']) as $child) {
                    $flat[] = $child;
                }
            }
        }
        return $flat;
    }

    // ── Render ────────────────────────────────────────────────────────────

    public function render()
    {
        $contents = Content::with('menuItem')
            ->when($this->search,       fn ($q) => $q->search($this->search))
            ->when($this->filterItem,   fn ($q) => $q->where('menu_item_id', $this->filterItem))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType,   fn ($q) => $q->where('type', $this->filterType))
            ->orderBy('menu_item_id')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15);

        $flatItems = $this->flattenTree($this->menuItemTree);

        return view('content::livewire.admin.content-manager', [
            'contents'  => $contents,
            'flatItems' => $flatItems,
        ]);
    }

    // ── CRUD ──────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $c = Content::findOrFail($id);
        $this->editMode          = true;
        $this->editingId         = $id;
        $this->menu_item_id      = $c->menu_item_id;
        $this->title             = $c->title;
        $this->slug              = $c->slug;
        $this->type              = $c->type;
        $this->body              = $c->body ?? '';
        $this->external_url      = $c->external_url ?? '';
        $this->status            = $c->status;
        $this->sort_order        = $c->sort_order;
        $this->published_at      = $c->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->existingFile      = $c->file_path;
        $this->existingFileName  = $c->file_name;
        $this->file              = null;
        $this->showModal         = true;
    }

    public function save(): void
    {
        $this->doValidate();

        $data = [
            'menu_item_id' => $this->menu_item_id,
            'title'        => $this->title,
            'slug'         => $this->slug ?: $this->buildFullSlug($this->menu_item_id, $this->title),
            'type'         => $this->type,
            'body'         => $this->type === 'content'  ? $this->body         : null,
            'external_url' => $this->type === 'external' ? $this->external_url : null,
            'status'       => $this->status,
            'sort_order'   => $this->sort_order,
            'published_at' => $this->published_at ?: null,
        ];

        if ($this->type === 'file' && $this->file) {
            if ($this->existingFile) Storage::disk('public')->delete($this->existingFile);
            $data['file_path'] = $this->file->store('content-files', 'public');
            $data['file_name'] = $this->file->getClientOriginalName();
            $data['file_size'] = $this->file->getSize();
            $data['file_mime'] = $this->file->getMimeType();
        }

        if ($this->editMode) {
            Content::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Content updated.');
        } else {
            Content::create($data);
            session()->flash('success', 'Content created.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function destroy(): void
    {
        $c = Content::findOrFail($this->deletingId);
        $c->deleted_by = auth()->id();
        $c->save();
        $c->delete();
        session()->flash('success', 'Content deleted.');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editMode = $this->showModal = false;
        $this->editingId = $this->deletingId = null;
        $this->menu_item_id = $this->sort_order = 0;
        $this->title = $this->slug = $this->body = $this->external_url = '';
        $this->status       = 'draft';
        $this->type         = 'content';
        $this->published_at = '';
        $this->file = $this->existingFile = $this->existingFileName = null;
        $this->resetValidation();
    }

    private function doValidate(): void
    {
        $uniqueRule = $this->editMode
            ? "unique:contents,slug,{$this->editingId},id,menu_item_id,{$this->menu_item_id}"
            : "unique:contents,slug,NULL,id,menu_item_id,{$this->menu_item_id}";

        $rules = [
            'menu_item_id' => 'required|integer|min:1|exists:menu_items,id',
            'title'        => 'required|string|max:300',
            'slug'         => ['required','string','max:500', $uniqueRule],
            'type'         => 'required|in:content,file,external',
            'status'       => 'required|in:draft,published,archived',
            'sort_order'   => 'nullable|integer|min:0',
            'published_at' => 'nullable|date',
        ];

        if ($this->type === 'content')  $rules['body']         = 'required|string';
        if ($this->type === 'external') $rules['external_url'] = 'required|url';
        if ($this->type === 'file' && (! $this->editMode || $this->file)) {
            $rules['file'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:' . config('content.max_file_size_kb', 10240);
        }

        $this->validate($rules);
    }
}