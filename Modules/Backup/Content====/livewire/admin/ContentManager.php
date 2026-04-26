<?php

namespace Modules\Content\Livewire\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\Content\Models\Content;
use Modules\Content\Support\MenuItemHelper;

class ContentManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterMenuItem = '';
    public $sortField = 'created_at';
    public $sortDir = 'desc';

    public $showForm = false;
    public $isEditing = false;
    public $editingId = null;
    public $confirmDelete = false;
    public $deletingId = null;

    public $menu_item_id = '';
    public $title = '';
    public $slug = '';
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
    public $existingFilePath = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterMenuItem' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'title' => ['required', 'string', 'max:300'],
            'slug' => [
                'nullable', 'string', 'max:300',
                Rule::unique('contents', 'slug')
                    ->ignore($this->editingId)
                    ->where(fn ($q) => $q->where('menu_item_id', $this->menu_item_id)),
            ],
            'type' => ['required', Rule::in(['content', 'file', 'external'])],
            'body' => [$this->type === 'content' ? 'required' : 'nullable', 'string'],
            'uploadedFile' => [$this->type === 'file' && !$this->isEditing ? 'required' : 'nullable', 'file', 'max:51200', 'mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,gif,zip,rar'],
            'external_url' => [$this->type === 'external' ? 'required' : 'nullable', 'url', 'max:1000'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:300'],
            'meta_description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterType() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterMenuItem() { $this->resetPage(); }

    public function updatedTitle($value)
    {
        if (!$this->isEditing || blank($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function getContentsProperty()
    {
        return Content::query()
            ->with('menuItem')
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterMenuItem, fn ($q) => $q->where('menu_item_id', $this->filterMenuItem))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate(15);
    }

    public function getLeafMenuItemsProperty()
    {
        return MenuItemHelper::leafNodes();
    }

    public function getLeafMenuOptionsProperty()
    {
        return $this->leafMenuItems->pluck('label', 'id')->all();
    }

    public function sortBy($field)
    {
        $allowed = ['title', 'type', 'status', 'created_at', 'published_at', 'sort_order'];
        if (!in_array($field, $allowed, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
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

        $this->editingId = $id;
        $this->menu_item_id = $content->menu_item_id;
        $this->title = $content->title;
        $this->slug = $content->slug;
        $this->type = $content->type;
        $this->body = $content->body ?? '';
        $this->external_url = $content->external_url ?? '';
        $this->status = $content->status;
        $this->published_at = $content->published_at?->format('Y-m-d\TH:i');
        $this->sort_order = $content->sort_order ?? 0;
        $this->meta_title = $content->meta_title ?? '';
        $this->meta_description = $content->meta_description ?? '';
        $this->existingFileName = $content->file_name ?? '';
        $this->existingFilePath = $content->file_path ?? '';
        $this->showForm = true;
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'menu_item_id' => $this->menu_item_id,
            'title' => $this->title,
            'slug' => Str::slug($this->slug ?: $this->title),
            'type' => $this->type,
            'body' => $this->type === 'content' ? $this->body : null,
            'external_url' => $this->type === 'external' ? $this->external_url : null,
            'status' => $this->status,
            'published_at' => $this->published_at ?: null,
            'sort_order' => (int) $this->sort_order,
            'meta_title' => $this->meta_title ?: null,
            'meta_description' => $this->meta_description ?: null,
        ];

        $content = $this->isEditing ? Content::findOrFail($this->editingId) : new Content();

        if ($this->type === 'file' && $this->uploadedFile) {
            if ($content->exists && $content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }

            $path = $this->uploadedFile->store('contents', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $this->uploadedFile->getClientOriginalName();
            $data['file_size'] = $this->uploadedFile->getSize();
            $data['file_mime'] = $this->uploadedFile->getMimeType();
        } elseif ($this->type !== 'file') {
            if ($content->exists && $content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }

            $data['file_path'] = null;
            $data['file_name'] = null;
            $data['file_size'] = null;
            $data['file_mime'] = null;
        }

        $content->fill($data)->save();

        session()->flash('success', $this->isEditing ? 'Content updated successfully.' : 'Content created successfully.');

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
        $next = match ($content->status) {
            'draft' => 'published',
            'published' => 'archived',
            default => 'draft',
        };

        $content->update(['status' => $next]);
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->confirmDelete = true;
    }

    public function cancelDelete()
    {
        $this->deletingId = null;
        $this->confirmDelete = false;
    }

    public function delete()
    {
        $content = Content::findOrFail($this->deletingId);

        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        $content->delete();

        $this->cancelDelete();
        session()->flash('success', 'Content deleted successfully.');
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->menu_item_id = '';
        $this->title = '';
        $this->slug = '';
        $this->type = 'content';
        $this->body = '';
        $this->uploadedFile = null;
        $this->external_url = '';
        $this->status = 'draft';
        $this->published_at = '';
        $this->sort_order = 0;
        $this->meta_title = '';
        $this->meta_description = '';
        $this->existingFileName = '';
        $this->existingFilePath = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('content::livewire.admin.content-manager');
    }
}
