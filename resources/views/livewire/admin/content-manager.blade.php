<div>
    {{-- Flash --}}
    @if(session('success'))
        <div class="alert-success mb-4" x-data x-init="setTimeout(() => $el.remove(), 3500)">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Content Management</h2>
            <p class="text-sm text-gray-500">Manage all articles, files, and links across sections.</p>
        </div>
        <button wire:click="create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Content
        </button>
    </div>

    {{-- Filters --}}
    <div class="card card-body mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search title or body…"
                class="form-input">

            <select wire:model.live="filterMenu" class="form-select">
                <option value="">All Sections</option>
                {{-- @foreach($this->leafMenus as $id => $path)
                    <option value="{{ $id }}">{{ $path }}</option>
                @endforeach --}}
            </select>

            <select wire:model.live="filterType" class="form-select">
                <option value="">All Types</option>
                <option value="content">Article / Text</option>
                <option value="file">File / PDF</option>
                <option value="external">External Link</option>
            </select>

            <select wire:model.live="filterStatus" class="form-select">
                <option value="">All Statuses</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>

    {{-- Content form modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-40 overflow-y-auto" x-data>
            <div class="flex min-h-screen items-start justify-center p-4 pt-10">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/40" wire:click="cancel"></div>

                {{-- Modal --}}
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl z-50">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-800">
                            {{ $isEditing ? 'Edit Content' : 'Add New Content' }}
                        </h3>
                        <button wire:click="cancel" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-5 max-h-[75vh] overflow-y-auto">

                        {{-- Section + Title --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Section (Leaf Menu) <span class="text-red-500">*</span></label>
                                <select wire:model="menu_id" class="form-select">
                                    <option value="">— Select section —</option>
                                    @foreach($this->leafMenus as $id => $path)
                                        <option value="{{ $id }}">{{ $path }}</option>
                                    @endforeach
                                </select>
                                @error('menu_id') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="form-label">Title <span class="text-red-500">*</span></label>
                                <input wire:model="title" type="text" class="form-input" placeholder="Content title">
                                @error('title') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Content type selector --}}
                        <div>
                            <label class="form-label">Content Type <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach(['content' => ['📝', 'Article / Text', 'Write rich HTML content'], 'file' => ['📄', 'File Upload', 'PDF, Word, Image, ZIP'], 'external' => ['🔗', 'External Link', 'Redirect to another URL']] as $val => [$emoji, $label, $hint])
                                    <label
                                        class="relative flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer transition-all
                                                  {{ $type === $val ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300' }}">
                                        <input wire:model.live="type" type="radio" value="{{ $val }}" class="sr-only">
                                        <span class="text-2xl">{{ $emoji }}</span>
                                        <span class="text-sm font-medium text-gray-800">{{ $label }}</span>
                                        <span class="text-xs text-gray-400 text-center">{{ $hint }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('type') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Dynamic: Article --}}
                        @if($type === 'content')
                            <div>
                                <label class="form-label">Body <span class="text-red-500">*</span></label>
                                <p class="form-hint">Rich-text editor — HTML is fully supported. Use the toolbar above to format content.</p>
                                <textarea wire:model="body" rows="10" class="form-textarea font-mono text-sm"
                                    placeholder="Enter HTML content here…"></textarea>
                                @error('body') <p class="form-error">{{ $message }}</p> @enderror
                            </div>



                            
                        @endif

                        {{-- Dynamic: File --}}
                        @if($type === 'file')
                            <div>
                                <label class="form-label">Upload File <span class="text-red-500">*</span></label>
                                @if($existingFileName)
                                    <div class="mb-2 flex items-center gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="text-sm text-gray-600">Current: <strong>{{ $existingFileName }}</strong></span>
                                        <span class="text-xs text-gray-400">Upload a new file to replace</span>
                                    </div>
                                @endif
                                <input wire:model="uploadedFile" type="file"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer border border-gray-300 rounded-lg p-1"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                                <p class="form-hint">Max 50 MB. Allowed: PDF, Word, Excel, Images, ZIP/RAR</p>
                                @error('uploadedFile') <p class="form-error">{{ $message }}</p> @enderror

                                {{-- Upload progress --}}
                                <div wire:loading wire:target="uploadedFile" class="mt-2">
                                    <div class="flex items-center gap-2 text-sm text-primary-600">
                                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                        </svg>
                                        Uploading…
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Dynamic: External --}}
                        @if($type === 'external')
                            <div>
                                <label class="form-label">External URL <span class="text-red-500">*</span></label>
                                <input wire:model="external_url" type="url" class="form-input"
                                    placeholder="https://example.gov.in/document">
                                <p class="form-hint">Clicking this content will redirect the visitor to this URL.</p>
                                @error('external_url') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        {{-- Status + Publish date --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Status</label>
                                <select wire:model="status" class="form-select">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Publish Date</label>
                                <input wire:model="published_at" type="datetime-local" class="form-input">
                                <p class="form-hint">Leave blank = publish immediately on status change.</p>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Sort Order</label>
                            <input wire:model="sort_order" type="number" min="0" class="form-input w-24">
                            <p class="form-hint">Lower number = appears first.</p>
                        </div>

                        {{-- SEO accordion --}}
                        <details class="border border-gray-200 rounded-lg">
                            <summary
                                class="px-4 py-3 text-sm font-medium text-gray-700 cursor-pointer hover:bg-gray-50 rounded-lg">
                                SEO Settings (optional)
                            </summary>
                            <div class="px-4 pb-4 pt-2 space-y-3">
                                <div>
                                    <label class="form-label">Meta Title</label>
                                    <input wire:model="meta_title" type="text" class="form-input"
                                        placeholder="Override page title for search engines">
                                </div>
                                <div>
                                    <label class="form-label">Meta Description</label>
                                    <textarea wire:model="meta_description" rows="2" class="form-textarea"
                                        placeholder="Brief description shown in search results (max 160 chars)"></textarea>
                                </div>
                            </div>
                        </details>

                    </div>

                    {{-- Footer --}}
                    <div
                        class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                        <button wire:click="cancel" class="btn-secondary">Cancel</button>
                        <button wire:click="save" wire:loading.attr="disabled" class="btn-primary">
                            <span wire:loading.remove
                                wire:target="save">{{ $isEditing ? 'Update Content' : 'Create Content' }}</span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                </svg>
                                Saving…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete confirm modal --}}
    @if($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/40" wire:click="cancelDelete"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full z-50 text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-800 mb-1">Delete Content?</h3>
                <p class="text-sm text-gray-500 mb-5">This content will be moved to trash. Associated files may be removed.
                </p>
                <div class="flex gap-3">
                    <button wire:click="cancelDelete" class="btn-secondary flex-1">Cancel</button>
                    <button wire:click="delete" class="btn-danger flex-1">Delete</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Content table --}}
    <div class="card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <button wire:click="sortBy('title')" class="flex items-center gap-1 hover:text-gray-800">
                                Title
                                @if($sortField === 'title')<span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>@endif
                            </button>
                        </th>
                        <th>Section</th>
                        <th>Type</th>
                        <th>
                            <button wire:click="sortBy('status')" class="flex items-center gap-1 hover:text-gray-800">
                                Status
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('created_at')"
                                class="flex items-center gap-1 hover:text-gray-800">
                                Created
                                @if($sortField === 'created_at')<span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>@endif
                            </button>
                        </th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>

                   
                    @forelse($this->contents as $item)
                        <tr>
                            <td class="max-w-xs">
                                <p class="font-medium text-gray-800 truncate">{{ $item->title }}</p>
                                @if($item->type === 'file')
                                    <p class="text-xs text-gray-400">{{ $item->file_name }} · {{ $item->file_size_human }}</p>
                                @elseif($item->type === 'external')
                                    <p class="text-xs text-gray-400 truncate">{{ $item->external_url }}</p>
                                @else
                                    <p class="text-xs text-gray-400">{{ Str::limit(strip_tags($item->body), 60) }}</p>
                                @endif
                            </td>
                            <td>
                                {{-- <span class="font-mono text-xs text-gray-500">{{ $item->menu?->full_slug }}</span> --}}
                            </td>
                            <td>
                                <span class="{{ $item->type_badge }}">{{ $item->type }}</span>
                            </td>
                            <td>
                                <button wire:click="toggleStatus({{ $item->id }})"
                                    class="focus:outline-none hover:opacity-80">
                                    <span class="{{ $item->status_badge }}">{{ $item->status }}</span>
                                </button>
                            </td>
                            <td class="text-xs text-gray-500">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            {{-- <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    @if($item->menu)
                                        <a href="{{ route('cms.item', [$item->menu->full_slug, $item->slug]) }}" target="_blank"
                                            class="btn btn-secondary btn-sm" title="View">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    @endif
                                    <button wire:click="edit({{ $item->id }})"
                                        class="btn btn-secondary btn-sm">Edit</button>
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                        class="btn btn-danger btn-sm">Delete</button>
                                </div>
                            </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-400">
                                No content found. Add some using the button above.
                            </td>
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