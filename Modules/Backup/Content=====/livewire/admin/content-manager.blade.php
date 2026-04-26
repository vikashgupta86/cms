<div>
    @if(session('success'))
        <div class="alert-success mb-4" x-data x-init="setTimeout(() => $el.remove(), 3500)">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Content Management</h2>
            <p class="text-sm text-gray-500">Gov-CMS style content manager mapped to your Menu / MenuItem structure.</p>
        </div>
        <button wire:click="create" class="btn-primary">Add Content</button>
    </div>

    <div class="card mb-5">
        <div class="card-body grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="form-label">Search</label>
                <input wire:model.live.debounce.300ms="search" type="text" class="form-input" placeholder="Search title or body...">
            </div>
            <div>
                <label class="form-label">Menu Item</label>
                <select wire:model.live="filterMenuItem" class="form-select">
                    <option value="">All menu items</option>
                    @foreach($this->leafMenuOptions as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Type</label>
                <select wire:model.live="filterType" class="form-select">
                    <option value="">All types</option>
                    <option value="content">Article / Text</option>
                    <option value="file">File</option>
                    <option value="external">External Link</option>
                </select>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select wire:model.live="filterStatus" class="form-select">
                    <option value="">All statuses</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
        </div>
    </div>

    @if($showForm)
        <div class="card mb-6 border-primary-200">
            <div class="card-header flex items-center justify-between bg-primary-50">
                <h3 class="text-sm font-semibold text-primary-800">{{ $isEditing ? 'Edit Content' : 'Create Content' }}</h3>
                <button wire:click="cancel" class="text-gray-500">✕</button>
            </div>

            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label">Menu Item <span class="text-red-500">*</span></label>
                    <select wire:model="menu_item_id" class="form-select">
                        <option value="">Select leaf menu item</option>
                        @foreach($this->leafMenuOptions as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('menu_item_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Title <span class="text-red-500">*</span></label>
                    <input wire:model="title" type="text" class="form-input" placeholder="Enter title">
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Slug</label>
                    <input wire:model="slug" type="text" class="form-input" placeholder="auto-generated if empty">
                    @error('slug') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Type <span class="text-red-500">*</span></label>
                    <select wire:model.live="type" class="form-select">
                        <option value="content">Article / Text</option>
                        <option value="file">File Upload</option>
                        <option value="external">External Link</option>
                    </select>
                    @error('type') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Status <span class="text-red-500">*</span></label>
                    <select wire:model="status" class="form-select">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                    @error('status') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Published At</label>
                    <input wire:model="published_at" type="datetime-local" class="form-input">
                    @error('published_at') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Sort Order</label>
                    <input wire:model="sort_order" type="number" min="0" class="form-input">
                    @error('sort_order') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                @if($type === 'content')
                    <div class="md:col-span-2">
                        <label class="form-label">Body <span class="text-red-500">*</span></label>
                        <textarea wire:model="body" rows="10" class="form-textarea" placeholder="Write content here..."></textarea>
                        @error('body') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if($type === 'file')
                    <div class="md:col-span-2">
                        <label class="form-label">Upload File {{ $isEditing ? '' : '*' }}</label>
                        <input wire:model="uploadedFile" type="file" class="form-input">
                        @if($existingFileName)
                            <p class="form-hint mt-2">Existing file: <strong>{{ $existingFileName }}</strong></p>
                        @endif
                        @error('uploadedFile') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if($type === 'external')
                    <div class="md:col-span-2">
                        <label class="form-label">External URL <span class="text-red-500">*</span></label>
                        <input wire:model="external_url" type="url" class="form-input" placeholder="https://example.com">
                        @error('external_url') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div>
                    <label class="form-label">Meta Title</label>
                    <input wire:model="meta_title" type="text" class="form-input">
                    @error('meta_title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Meta Description</label>
                    <textarea wire:model="meta_description" rows="3" class="form-textarea"></textarea>
                    @error('meta_description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="card-footer flex gap-3">
                <button wire:click="save" wire:loading.attr="disabled" class="btn-primary">
                    {{ $isEditing ? 'Update Content' : 'Save Content' }}
                </button>
                <button wire:click="cancel" class="btn-secondary">Cancel</button>
            </div>
        </div>
    @endif

    @if($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-semibold mb-2">Delete Content</h3>
                <p class="text-sm text-gray-600 mb-4">Are you sure you want to delete this content?</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelDelete" class="btn-secondary">Cancel</button>
                    <button wire:click="delete" class="btn-danger">Delete</button>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="table-wrap overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th><button wire:click="sortBy('title')">Title</button></th>
                        <th>Menu Item</th>
                        <th><button wire:click="sortBy('type')">Type</button></th>
                        <th><button wire:click="sortBy('status')">Status</button></th>
                        <th><button wire:click="sortBy('published_at')">Published</button></th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->contents as $item)
                        <tr>
                            <td>
                                <div class="font-medium text-gray-800">{{ $item->title }}</div>
                                @if($item->type === 'content' && $item->body)
                                    <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit(strip_tags($item->body), 90) }}</p>
                                @elseif($item->type === 'file')
                                    <p class="text-xs text-gray-500">{{ $item->file_name }} @if($item->file_size_human) · {{ $item->file_size_human }} @endif</p>
                                @elseif($item->type === 'external')
                                    <p class="text-xs text-gray-500">{{ $item->external_url }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="font-mono text-xs text-gray-500">{{ $item->menu_display_path ?? '-' }}</span>
                            </td>
                            <td><span class="{{ $item->type_badge }}">{{ ucfirst($item->type) }}</span></td>
                            <td>
                                <button wire:click="toggleStatus({{ $item->id }})">
                                    <span class="{{ $item->status_badge }}">{{ ucfirst($item->status) }}</span>
                                </button>
                            </td>
                            <td class="text-xs text-gray-500">{{ $item->published_at?->format('d M Y h:i A') ?? '-' }}</td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    @if($item->menu_path)
                                        <a href="{{ route('cms.item', [$item->menu_path, $item->slug]) }}" target="_blank" class="btn btn-secondary btn-sm">View</a>
                                    @endif
                                    <button wire:click="edit({{ $item->id }})" class="btn btn-secondary btn-sm">Edit</button>
                                    <button wire:click="confirmDelete({{ $item->id }})" class="btn btn-danger btn-sm">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-400">No content found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->contents->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $this->contents->links() }}
            </div>
        @endif
    </div>
</div>
