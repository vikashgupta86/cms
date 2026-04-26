<?php

namespace Modules\Content\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Modules\Content\Models\Content;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ContentManager extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public $search = '';
    public $filterMenu = '';
    public $filterType = '';
    public $filterStatus = '';

    // Sorting
    public $sortField = 'created_at';
    public $sortDir = 'desc';

    // Form state
    public $showForm = false;
    public $isEditing = false;
    public $editingId = null;

    // Form fields
    public $menu_item_id = '';
    public $title = '';
    public $type = 'content';
    public $body = '';
    public $uploadedFile = null;
    public $external_url = '';
    public $status = 'draft';
    public $published_at = '';
    public $sort_order = 0;
    public $meta_title = '';
    public $meta_description = '';
    public $existingFileName = '';

    // Delete confirm
    public $confirmDelete = false;
    public $deletingId = null;

    protected function rules()
    {
        return [
            'menu_item_id' => 'required|integer',
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:content,file,external',
            'body'         => $this->type === 'content' ? 'required|string' : 'nullable',
            'uploadedFile' => $this->type === 'file' && !$this->isEditing ? 'required|file|max:51200' : 'nullable|file|max:51200',
            'external_url' => $this->type === 'external' ? 'required|url' : 'nullable',
            'status'       => 'required|in:draft,published,archived',
        ];
    }

    // Reset pagination when filters change
    public function updatedSearch()    { $this->resetPage(); }
    public function updatedFilterMenu(){ $this->resetPage(); }
    public function updatedFilterType(){ $this->resetPage(); }
    public function updatedFilterStatus(){ $this->resetPage(); }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
    }

    // Computed property — contents list
    public function getContentsProperty()
    {
        return Content::query()
            ->with('menuItem')
            ->when($this->search, fn($q) =>
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('body', 'like', "%{$this->search}%")
            )
            ->when($this->filterMenu,   fn($q) => $q->where('menu_item_id', $this->filterMenu))
            ->when($this->filterType,   fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate(15);
    }

    // Computed property — leaf menus for dropdown
    public function getLeafMenusProperty()
    {
        return \Modules\Menu\Models\MenuItem::whereDoesntHave('children')->pluck('name', 'id');
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
    }

    public function edit($id)
    {
        $content = Content::findOrFail($id);
        $this->editingId      = $id;
        $this->menu_item_id   = $content->menu_item_id;
        $this->title          = $content->title;
        $this->type           = $content->type;
        $this->body           = $content->body;
        $this->external_url   = $content->external_url;
        $this->status         = $content->status;
        $this->published_at   = $content->published_at?->format('Y-m-d\TH:i');
        $this->sort_order     = $content->sort_order;
        $this->meta_title     = $content->meta_title;
        $this->meta_description = $content->meta_description;
        $this->existingFileName = $content->file_name;
        $this->showForm  = true;
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'menu_item_id'     => $this->menu_item_id,
            'title'            => $this->title,
            'slug'             => Str::slug($this->title),
            'type'             => $this->type,
            'body'             => $this->type === 'content' ? $this->body : null,
            'external_url'     => $this->type === 'external' ? $this->external_url : null,
            'status'           => $this->status,
            'published_at'     => $this->published_at ?: null,
            'sort_order'       => $this->sort_order,
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
        ];

        // File upload handle
        if ($this->type === 'file' && $this->uploadedFile) {
            $path = $this->uploadedFile->store('contents', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $this->uploadedFile->getClientOriginalName();
            $data['file_size'] = $this->uploadedFile->getSize();
        }

        if ($this->isEditing) {
            Content::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Content updated successfully!');
        } else {
            Content::create($data);
            session()->flash('success', 'Content created successfully!');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function toggleStatus($id)
    {
        $content = Content::findOrFail($id);
        $next = match($content->status) {
            'draft'     => 'published',
            'published' => 'archived',
            default     => 'draft',
        };
        $content->update(['status' => $next]);
    }

    public function confirmDelete($id)
    {
        $this->deletingId    = $id;
        $this->confirmDelete = true;
    }

    public function cancelDelete()
    {
        $this->deletingId    = null;
        $this->confirmDelete = false;
    }

    public function delete()
    {
        Content::findOrFail($this->deletingId)->delete();
        $this->cancelDelete();
        session()->flash('success', 'Content deleted!');
    }

    private function resetForm()
    {
        $this->editingId      = null;
        $this->menu_item_id   = '';
        $this->title          = '';
        $this->type           = 'content';
        $this->body           = '';
        $this->uploadedFile   = null;
        $this->external_url   = '';
        $this->status         = 'draft';
        $this->published_at   = '';
        $this->sort_order     = 0;
        $this->meta_title     = '';
        $this->meta_description = '';
        $this->existingFileName = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('content::livewire.admin.content-manager');
    }
}