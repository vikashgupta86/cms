<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\Content\Models\Content;
use Modules\Menu\Models\Menu;
use Modules\Menu\Models\MenuItem;

class ContentManager extends Component
{
    use WithFileUploads, WithPagination;

    // Form state
    public bool $showForm = false;

    public bool $isEditing = false;

    public ?int $editingId = null;

    // Form fields
    public ?int $menu_id = null;

    public string $title = '';

    public string $type = 'content';

    public string $body = '';

    public $uploadedFile = null;

    public string $external_url = '';

    public string $status = 'draft';

    public ?string $published_at = null;

    public int $sort_order = 0;

    public string $meta_title = '';

    public string $meta_description = '';

    public string $existingFileName = '';

    // Filter & sort state
    public string $search = '';

    public int|string $filterMenu = '';

    public string $filterType = '';

    public string $filterStatus = '';

    public string $sortField = 'created_at';

    public string $sortDir = 'desc';

    // Delete confirmation
    public bool $confirmDelete = false;

    public ?int $deleteId = null;

    /**
     * Get menus with menu items for section selection
     * Fetches both Menu and MenuItem models to show hierarchy
     */
    #[Computed]
    public function leafMenus(): array
    {
        return Menu::orderBy('name')
            ->get()
            ->mapWithKeys(fn ($menu) => [$menu->id => "📋 {$menu->name}"])
            ->toArray();
    }

    /**
     * Get menu items for a specific menu to show hierarchy
     */
    #[Computed]
    public function menuItems(): array
    {
        if (! $this->menu_id) {
            return [];
        }

        return MenuItem::where('menu_id', $this->menu_id)
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(function ($item) {
                $indent = $item->parent_id ? '  ├─ ' : '  └─ ';

                return [$item->id => $indent.$item->title];
            })
            ->toArray();
    }

    /**
     * Get filtered and paginated contents
     */
    #[Computed]
    public function contents(): \Illuminate\Pagination\LengthAwarePaginator
    {
        
        $query = Content::query();
         if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%")
                ->orWhere('body', 'like', "%{$this->search}%");
        }

        if ($this->filterMenu) {
            $query->where('menu_id', $this->filterMenu);
        }

        // if ($this->filterType) {
        //     $query->where('type', $this->filterType);
        // }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $data= $query->orderBy($this->sortField, $this->sortDir)
            ->paginate(15);
           
        return $data;
    }

    /**
     * Open create form
     */
    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    /**
     * Open edit form
     */
    public function edit(int $id): void
    {
        $content = Content::findOrFail($id);
        $this->editingId = $id;
        $this->isEditing = true;
        $this->showForm = true;

        $this->menu_id = $content->menu_id;
        $this->title = $content->title;
        $this->type = $content->type;
        $this->body = $content->body ?? '';
        $this->external_url = $content->external_url ?? '';
        $this->status = $content->status;
        $this->published_at = $content->published_at?->format('Y-m-d\TH:i');
        $this->sort_order = $content->sort_order;
        $this->meta_title = $content->meta_title ?? '';
        $this->meta_description = $content->meta_description ?? '';
        $this->existingFileName = $content->file_name ?? '';
    }

    /**
     * Cancel form
     */
    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    /**
     * Save content
     */
    public function save(): void
    {
        $validated = $this->validate([
            'menu_id' => 'required|exists:menus,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:content,file,external',
            'body' => 'required_if:type,content|nullable|string',
            'uploadedFile' => 'required_if:type,file|nullable|file|max:51200',
            'external_url' => 'required_if:type,external|nullable|url',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date_format:Y-m-d\TH:i',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ]);

        if ($this->isEditing) {
            $content = Content::findOrFail($this->editingId);
            $content->update($validated);

            if ($this->uploadedFile) {
                $path = $this->uploadedFile->store('contents', 'public');
                $content->update([
                    'file_name' => $this->uploadedFile->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $this->uploadedFile->getSize(),
                ]);
            }

            session()->flash('success', 'Content updated successfully!');
        } else {
            $data = $validated;

            if ($this->uploadedFile) {
                $path = $this->uploadedFile->store('contents', 'public');
                $data['file_name'] = $this->uploadedFile->getClientOriginalName();
                $data['file_path'] = $path;
                $data['file_size'] = $this->uploadedFile->getSize();
            }

            if ($data['type'] === 'content') {
                $data['slug'] = Str::slug($this->title);
            }

            Content::create($data);
            session()->flash('success', 'Content created successfully!');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    /**
     * Delete content
     */
    public function delete(): void
    {
        if ($this->deleteId) {
            Content::find($this->deleteId)?->delete();
            session()->flash('success', 'Content deleted successfully!');
        }

        $this->confirmDelete = false;
        $this->deleteId = null;
        $this->resetPage();
    }

    /**
     * Confirm delete
     */
    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->confirmDelete = true;
    }

    /**
     * Cancel delete
     */
    public function cancelDelete(): void
    {
        $this->confirmDelete = false;
        $this->deleteId = null;
    }

    /**
     * Toggle status
     */
    public function toggleStatus(int $id): void
    {
        $content = Content::findOrFail($id);
        $newStatus = $content->status === 'published' ? 'draft' : 'published';
        $content->update(['status' => $newStatus]);
    }

    /**
     * Sort by field
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'desc';
        }
    }

    /**
     * Reset form
     */
    private function resetForm(): void
    {
        $this->menu_id = null;
        $this->title = '';
        $this->type = 'content';
        $this->body = '';
        $this->uploadedFile = null;
        $this->external_url = '';
        $this->status = 'draft';
        $this->published_at = null;
        $this->sort_order = 0;
        $this->meta_title = '';
        $this->meta_description = '';
        $this->existingFileName = '';
        $this->isEditing = false;
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.admin.content-manager');
    }
}
