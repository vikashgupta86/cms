<?php

namespace Modules\Menu\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Menu\Models\Menu;
use Modules\Menu\Models\MenuItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuItemComponent extends Component
{
    use WithFileUploads;

    public $menu_id;
    public $parent_id;
    public $type = 'link';
    public $name;
    public $slug;
    public $sort_order = 0;
    public $url;
    public $route_name;
    public $route_parameters;
    public $description;
    public $icon;
    public $badge_text;
    public $badge_color;
    public $opens_new_tab = 0;
    public $css_classes;
    public $html_attributes;
    public $permissions = [];
    public $roles = [];
    public $status = 1;
    public $is_active = 1;
    public $is_visible = 1;
    public $locale;
    public $meta_title;
    public $custom_data;
    public $note;

    // Upload inputs — hold the TemporaryUploadedFile objects (no live upload)
    public $upload_file;
    public $upload_file_icon;

    // Stored paths — saved to DB
    public $file;
    public $file_icon;

    public $content = '';

    public $existingFile;
    public $existingFileIcon;

    public $menus = [];
    public $parent_items = [];
    public $available_permissions = [];
    public $available_roles = [];

    public $menuItem;

    public function mount($menuItem = null, $menu_id = null): void
    {
        $this->menuItem = $menuItem;

        if ($menu_id && ! $this->menuItem) {
            $this->menu_id = $menu_id;
        }

        $this->loadDropdownData();

        if ($this->menuItem) {
            $this->populateFormFromMenuItem();
        }
    }

    public function updatedMenuId(): void
    {
        $this->parent_id = null;
        $this->loadParentItems();
    }

    public function updatedType(): void
    {
        if (in_array($this->type, ['divider', 'heading', 'dropdown'])) {
            $this->url = '';
            $this->route_name = '';
            $this->route_parameters = '';
        }

        if ($this->type !== 'file') {
            $this->upload_file     = null;
            $this->upload_file_icon = null;
        }

        if ($this->type !== 'content') {
            $this->content = '';
        }
    }

    public function updatedName(): void
    {
        if (empty($this->slug) && ! empty($this->name)) {
            $this->generateSlug();
        }
    }

    protected function loadDropdownData(): void
    {
        $this->menus = Menu::where('status', 1)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->available_permissions = Permission::pluck('name', 'name')->toArray();
        $this->available_roles       = Role::pluck('name', 'name')->toArray();

        if ($this->menu_id) {
            $this->loadParentItems();
        }
    }

    protected function loadParentItems(): void
    {
        if (! $this->menu_id) {
            $this->parent_items = [];
            return;
        }

        $excludeIds = [];

        if ($this->menuItem) {
            $excludeIds = array_merge(
                [$this->menuItem->id],
                $this->getDescendantIds($this->menuItem->id)
            );
        }

        $allItems = MenuItem::where('menu_id', $this->menu_id)
            ->where('is_active', true)
            ->where('is_visible', true)
            ->where('type', '!=', 'divider')
            ->when(! empty($excludeIds), fn ($q) => $q->whereNotIn('id', $excludeIds))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'type'])
            ->toArray();

        $this->parent_items = $this->buildTreeOptions($allItems);

        $validIds = array_column($this->parent_items, 'id');

        if ($this->parent_id && ! in_array($this->parent_id, $validIds)) {
            $this->parent_id = null;
        }
    }

    private function buildTreeOptions(array $allItems, ?int $parentId = null, int $depth = 0): array
    {
        $options = [];

        foreach ($allItems as $item) {
            if ($item['parent_id'] != $parentId) {
                continue;
            }

            $prefix = $depth === 0
                ? ''
                : str_repeat('&nbsp;&nbsp;', $depth - 1) . '└─';

            $options[] = [
                'id'       => $item['id'],
                'name'     => $item['name'],
                'prefix'   => $prefix,
                'disabled' => false,
                'depth'    => $depth,
                'type'     => $item['type'],
            ];

            $children = $this->buildTreeOptions($allItems, $item['id'], $depth + 1);
            $options  = array_merge($options, $children);
        }

        return $options;
    }

    private function getDescendantIds(int $parentId, int $depth = 0, int $maxDepth = 10): array
    {
        if ($depth >= $maxDepth) {
            return [];
        }

        $descendants = [];
        $children    = MenuItem::where('parent_id', $parentId)->pluck('id');

        foreach ($children as $childId) {
            $descendants[] = $childId;
            $descendants   = array_merge(
                $descendants,
                $this->getDescendantIds($childId, $depth + 1, $maxDepth)
            );
        }

        return $descendants;
    }

    protected function populateFormFromMenuItem(): void
    {
        $m = $this->menuItem;

        $this->menu_id = $m->menu_id;
        $this->loadParentItems();

        $this->parent_id         = $m->parent_id;
        $this->type              = $m->type ?? 'link';
        $this->name              = $m->name;
        $this->slug              = $m->slug;
        $this->sort_order        = $m->sort_order ?? 0;
        $this->url               = $m->url;
        $this->route_name        = $m->route_name;
        $this->route_parameters  = $m->route_parameters;
        $this->description       = $m->description;
        $this->icon              = $m->icon;
        $this->badge_text        = $m->badge_text;
        $this->badge_color       = $m->badge_color;
        $this->opens_new_tab     = $m->opens_new_tab ? 1 : 0;
        $this->css_classes       = $m->css_classes;
        $this->html_attributes   = $m->html_attributes;
        $this->permissions       = $m->permissions ?? [];
        $this->roles             = $m->roles ?? [];
        $this->status            = $m->status ?? 1;
        $this->is_active         = $m->is_active ? 1 : 0;
        $this->is_visible        = $m->is_visible ? 1 : 0;
        $this->locale            = $m->locale;
        $this->meta_title        = $m->meta_title;
        $this->custom_data       = $m->custom_data;
        $this->note              = $m->note;
        $this->content           = $m->content ?? '';

        // Stored paths from DB
        $this->file              = $m->file;
        $this->existingFile      = $m->file;

        $this->file_icon         = $m->file_icon;
        $this->existingFileIcon  = $m->file_icon;

        // Upload inputs always start empty
        $this->upload_file      = null;
        $this->upload_file_icon = null;
    }

    public function resetForm(): void
    {
        $this->reset([
            'menu_id',
            'parent_id',
            'name',
            'slug',
            'sort_order',
            'url',
            'route_name',
            'route_parameters',
            'description',
            'icon',
            'badge_text',
            'badge_color',
            'css_classes',
            'html_attributes',
            'permissions',
            'roles',
            'locale',
            'meta_title',
            'custom_data',
            'note',
            'upload_file',
            'upload_file_icon',
            'file',
            'file_icon',
            'content',
            'existingFile',
            'existingFileIcon',
        ]);

        $this->type         = 'link';
        $this->status       = 1;
        $this->is_active    = 1;
        $this->is_visible   = 1;
        $this->opens_new_tab = 0;
        $this->sort_order   = 0;

        $this->resetErrorBag();
        $this->loadDropdownData();

        $this->dispatch('reset-editor');
    }

    protected function rules(): array
    {
        $rules = [
            'menu_id'          => 'required|exists:menus,id',
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:link,dropdown,divider,heading,external,file,content',
            'status'           => 'required|in:0,1,2',
            'sort_order'       => 'nullable|integer|min:0',
            'url'              => 'nullable|string|max:500',
            'route_name'       => 'nullable|string|max:255',
            'route_parameters' => 'nullable|json',
            'html_attributes'  => 'nullable|json',
            'custom_data'      => 'nullable|json',
            'description'      => 'nullable|string|max:1000',
            'icon'             => 'nullable|string|max:100',
            'badge_text'       => 'nullable|string|max:50',
            'badge_color'      => 'nullable|string|max:20',
            'css_classes'      => 'nullable|string|max:500',
            'locale'           => 'nullable|string|max:5',
            'meta_title'       => 'nullable|string|max:255',
            'note'             => 'nullable|string|max:1000',
        ];

        if ($this->slug) {
            $uniqueRule = 'unique:menu_items,slug';

            if ($this->menuItem) {
                $uniqueRule .= ',' . $this->menuItem->id;
            }

            $rules['slug'] = 'nullable|string|max:255|' . $uniqueRule;
        } else {
            $rules['slug'] = 'nullable|string|max:255';
        }

        if ($this->type === 'file') {
            // upload_file is required only when there is no existing file
            $rules['upload_file'] = $this->existingFile
                ? 'nullable|file|max:10240'
                : 'required|file|max:10240';

            // upload_file_icon is always optional
            $rules['upload_file_icon'] = 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:2048';
        }

        if ($this->type === 'content') {
            $rules['content'] = 'nullable|string';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'menu_id.required'          => 'Please select a menu.',
            'menu_id.exists'            => 'The selected menu no longer exists.',
            'name.required'             => 'Display name is required.',
            'type.required'             => 'Please choose an item type.',
            'status.required'           => 'Please set a publication status.',
            'upload_file.required'      => 'Please upload a file for this menu item.',
            'upload_file.max'           => 'The uploaded file must not exceed 10 MB.',
            'upload_file_icon.max'      => 'The icon must not exceed 2 MB.',
            'upload_file_icon.mimes'    => 'The icon must be jpg, jpeg, png, webp, or svg.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $this->validateJsonFields();

            if (empty($this->slug) && ! empty($this->name)) {
                $this->slug = Str::slug($this->name);
            }

            $this->handleFileUpload();
            $this->handleFileIconUpload();

            $data = $this->prepareDataForSave();

            if ($this->menuItem) {
                $this->menuItem->update($data);

                $message    = 'Menu item "' . $this->name . '" updated successfully!';
                $routeParam = $this->menuItem->id;

                logUserAccess('MenuItem Update | Id: ' . $this->menuItem->id);
            } else {
                $created = MenuItem::create($data);

                $message    = 'Menu item "' . $this->name . '" created successfully!';
                $routeParam = $created->id;

                logUserAccess('MenuItem Store | Id: ' . $created->id);
            }

            session()->flash('flash_success', $message);

            return redirect()->route('backend.menuitems.show', $routeParam);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;

        } catch (\Exception $e) {
            $this->addError('general', 'Error saving menu item: ' . $e->getMessage());
            session()->flash('flash_danger', 'Error saving menu item: ' . $e->getMessage());
        }
    }

    protected function handleFileUpload(): void
    {
        if ($this->type !== 'file') {
            $this->file = null;
            return;
        }

        if ($this->upload_file) {
            // Delete old file if one exists
            if ($this->existingFile && Storage::disk('public')->exists($this->existingFile)) {
                Storage::disk('public')->delete($this->existingFile);
            }

            // Store new file and save the path into $this->file
            $this->file = $this->upload_file->store('menu-files', 'public');

        } elseif ($this->existingFile) {
            // No new upload — keep the existing path
            $this->file = $this->existingFile;
        }
    }

    protected function handleFileIconUpload(): void
    {
        // if ($this->type !== 'file') {
        //     $this->file_icon = null;
        //     return;
        // }

        if ($this->upload_file_icon) {
            // Delete old icon if one exists
            if ($this->existingFileIcon && Storage::disk('public')->exists($this->existingFileIcon)) {
                Storage::disk('public')->delete($this->existingFileIcon);
            }

            // Store new icon and save the path into $this->file_icon
            $this->file_icon = $this->upload_file_icon->store('menu-file-icons', 'public');

        } elseif ($this->existingFileIcon) {
            // No new upload — keep the existing path
            $this->file_icon = $this->existingFileIcon;
        }
    }

    protected function validateJsonFields(): void
    {
        $jsonFields = [
            'route_parameters' => 'Route parameters',
            'html_attributes'  => 'HTML attributes',
            'custom_data'      => 'Custom data',
        ];

        foreach ($jsonFields as $field => $label) {
            $value = $this->$field;

            if (! empty(trim((string) $value))) {
                json_decode($value, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->addError($field, "$label must be valid JSON.");
                }
            }
        }
    }

    protected function prepareDataForSave(): array
    {
        return [
            'menu_id'          => $this->menu_id,
            'parent_id'        => $this->parent_id ?: null,
            'type'             => $this->type,
            'name'             => $this->name,
            'slug'             => $this->slug,
            'sort_order'       => (int) ($this->sort_order ?: 0),
            'url'              => $this->url ?: null,
            'route_name'       => $this->route_name ?: null,
            'route_parameters' => $this->route_parameters ?: null,
            'description'      => $this->description ?: null,
            'icon'             => $this->icon ?: null,
            'badge_text'       => $this->badge_text ?: null,
            'badge_color'      => $this->badge_color ?: null,
            'opens_new_tab'    => (bool) $this->opens_new_tab,
            'css_classes'      => $this->css_classes ?: null,
            'html_attributes'  => $this->html_attributes ?: null,
            'permissions'      => $this->permissions ?: null,
            'roles'            => $this->roles ?: null,
            'status'           => (int) $this->status,
            'is_active'        => (bool) $this->is_active,
            'is_visible'       => (bool) $this->is_visible,
            'locale'           => $this->locale ?: null,
            'meta_title'       => $this->meta_title ?: null,
            'custom_data'      => $this->custom_data ?: null,
            'note'             => $this->note ?: null,

            'content'   => ($this->type === 'content') ? ($this->content ?: null) : null,

            // $this->file and $this->file_icon now always hold stored paths (strings)
            'file'      => ($this->type === 'file') ? ($this->file ?: null) : null,
            'file_icon' =>  $this->file_icon ?: null,
        ];
    }

    public function generateSlug(): void
    {
        if (! empty($this->name)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function render()
    {
        return view('menu::livewire.menu-item-component');
    }
}