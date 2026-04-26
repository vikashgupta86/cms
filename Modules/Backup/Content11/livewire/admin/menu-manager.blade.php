<div>
    {{-- Flash --}}
    @if(session('success'))
        <div class="alert-success mb-4" x-data x-init="setTimeout(() => $el.remove(), 3500)">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-error mb-4">{{ session('error') }}</div>
    @endif

    {{-- Header bar --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Menu Management</h2>
            <p class="text-sm text-gray-500">Build your site structure. Content-capable sections are leaf nodes.</p>
        </div>
        <button wire:click="create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Menu
        </button>
    </div>

    {{-- Search --}}
    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search" type="text"
               placeholder="Search menus..." class="form-input max-w-xs">
    </div>

    {{-- Slide-in form --}}
    @if($showForm)
    <div class="card mb-6 border-primary-200">
        <div class="card-header flex items-center justify-between bg-primary-50">
            <h3 class="text-sm font-semibold text-primary-800">
                {{ $isEditing ? 'Edit Menu' : 'New Menu' }}
            </h3>
            <button wire:click="cancel" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Parent --}}
                <div>
                    <label class="form-label">Parent Menu</label>
                    <select wire:model="parent_id" class="form-select">
                        <option value="">— Root (top level) —</option>
                        @foreach($this->parentOptions as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Title --}}
                <div>
                    <label class="form-label">Title <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text" class="form-input" placeholder="e.g. Tenders">
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label class="form-label">Slug</label>
                    <input wire:model="slug" type="text" class="form-input" placeholder="auto-generated if empty">
                    <p class="form-hint">Leave blank to auto-generate from title.</p>
                    @error('slug') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Icon --}}
                <div>
                    <label class="form-label">Icon (emoji or class)</label>
                    <input wire:model="icon" type="text" class="form-input" placeholder="📄 or heroicon-document">
                    @error('icon') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="form-label">Description</label>
                    <textarea wire:model="description" rows="2" class="form-textarea" placeholder="Optional short description"></textarea>
                </div>

                {{-- Sort & Status --}}
                <div>
                    <label class="form-label">Sort Order</label>
                    <input wire:model="sort_order" type="number" min="0" class="form-input w-28">
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                        <span class="text-sm text-gray-700 font-medium">Active</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer flex gap-3">
            <button wire:click="save" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Update Menu' : 'Create Menu' }}</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
            <button wire:click="cancel" class="btn-secondary">Cancel</button>
        </div>
    </div>
    @endif

    {{-- Menu tree table --}}
    <div class="card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Slug / Path</th>
                        <th>Depth</th>
                        <th>Children</th>
                        <th>Contents</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->menus as $menu)
                        {{-- Root row --}}
                        <tr class="bg-gray-50/70">
                            <td class="font-semibold text-gray-800">
                                <div class="flex items-center gap-2">
                                    @if($menu->icon)<span class="text-base">{{ $menu->icon }}</span>@endif
                                    {{ $menu->title }}
                                </div>
                            </td>
                            <td class="font-mono text-xs text-gray-500">{{ $menu->full_slug }}</td>
                            <td><span class="badge badge-gray">Root</span></td>
                            <td>{{ $menu->children->count() }}</td>
                            <td>{{ $menu->contents()->count() }}</td>
                            <td>
                                <button wire:click="toggleActive({{ $menu->id }})" class="focus:outline-none">
                                    @if($menu->is_active)
                                        <span class="badge-green">Active</span>
                                    @else
                                        <span class="badge-gray">Inactive</span>
                                    @endif
                                </button>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    <button wire:click="edit({{ $menu->id }})" class="btn btn-secondary btn-sm">Edit</button>
                                    <button wire:click="delete({{ $menu->id }})"
                                            wire:confirm="Delete '{{ $menu->title }}'? This cannot be undone."
                                            class="btn btn-danger btn-sm">Delete</button>
                                </div>
                            </td>
                        </tr>

                        {{-- Children --}}
                        @foreach($menu->children as $child)
                        <tr>
                            <td class="pl-8">
                                <div class="flex items-center gap-2 text-gray-700">
                                    <span class="text-gray-300">↳</span>
                                    @if($child->icon)<span>{{ $child->icon }}</span>@endif
                                    {{ $child->title }}
                                    @if($child->isLeaf()) <span class="badge badge-blue text-xs">leaf</span> @endif
                                </div>
                            </td>
                            <td class="font-mono text-xs text-gray-500">{{ $child->full_slug }}</td>
                            <td><span class="badge badge-gray">Child</span></td>
                            <td>{{ $child->children->count() }}</td>
                            <td>{{ $child->contents()->count() }}</td>
                            <td>
                                <button wire:click="toggleActive({{ $child->id }})">
                                    <span class="{{ $child->is_active ? 'badge-green' : 'badge-gray' }}">{{ $child->is_active ? 'Active' : 'Inactive' }}</span>
                                </button>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    <button wire:click="edit({{ $child->id }})" class="btn btn-secondary btn-sm">Edit</button>
                                    <button wire:click="delete({{ $child->id }})"
                                            wire:confirm="Delete '{{ $child->title }}'?"
                                            class="btn btn-danger btn-sm">Delete</button>
                                </div>
                            </td>
                        </tr>

                            {{-- Grandchildren --}}
                            @foreach($child->children as $grandchild)
                            <tr>
                                <td class="pl-16">
                                    <div class="flex items-center gap-2 text-gray-600 text-sm">
                                        <span class="text-gray-300">↳</span>
                                        {{ $grandchild->title }}
                                        <span class="badge badge-blue text-xs">leaf</span>
                                    </div>
                                </td>
                                <td class="font-mono text-xs text-gray-400">{{ $grandchild->full_slug }}</td>
                                <td><span class="badge badge-gray">Sub</span></td>
                                <td>—</td>
                                <td>{{ $grandchild->contents()->count() }}</td>
                                <td>
                                    <span class="{{ $grandchild->is_active ? 'badge-green' : 'badge-gray' }}">{{ $grandchild->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-1">
                                        <button wire:click="edit({{ $grandchild->id }})" class="btn btn-secondary btn-sm">Edit</button>
                                        <button wire:click="delete({{ $grandchild->id }})"
                                                wire:confirm="Delete '{{ $grandchild->title }}'?"
                                                class="btn btn-danger btn-sm">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-400">
                                No menus found. Click "Add Menu" to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
