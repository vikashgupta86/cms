<?php

namespace Modules\Content\Http\Controllers\Backend;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Content\Http\Requests\StoreContentRequest;
use Modules\Content\Http\Requests\UpdateContentRequest;
use Modules\Content\Models\Content;
use Modules\Menu\Models\Menu;
use Modules\Menu\Models\MenuItem;

class ContentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:content.index')->only(['index']);
        $this->middleware('can:content.create')->only(['create', 'store']);
        $this->middleware('can:content.edit')->only(['edit', 'update']);
        $this->middleware('can:content.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Content::with('menu')->orderBy('sort_order')->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($menuId = $request->get('menu_id')) {
            $query->where('menu_id', $menuId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $contents = $query->paginate(20)->withQueryString();
        $menus    = MenuItem::where('is_active', true)->orderBy('name')->get();

        return view('content::backend.contents.index', compact('contents', 'menus'));
    }

    public function create()
    {
        $menus = Menu::where('is_active', true)->orderBy('name')->get();

        return view('content::backend.contents.create', compact('menus'));
    }

    public function store(StoreContentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $file        = $request->file('file');
            $stored      = $file->store('content-files', 'public');
            $data['file_path'] = $stored;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_mime'] = $file->getMimeType();
        }

        unset($data['file']);

        Content::create($data);

        return redirect()->route('backend.contents.index')
            ->with('success', 'Content created successfully.');
    }

    public function edit(Content $content)
    {
        $menus = Menu::where('is_active', true)->orderBy('name')->get();

        return view('content::backend.contents.edit', compact('content', 'menus'));
    }

    public function update(UpdateContentRequest $request, Content $content): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            // Delete old file
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }

            $file        = $request->file('file');
            $stored      = $file->store('content-files', 'public');
            $data['file_path'] = $stored;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_mime'] = $file->getMimeType();
        }

        unset($data['file']);

        $content->update($data);

        return redirect()->route('backend.contents.index')
            ->with('success', 'Content updated successfully.');
    }

    public function destroy(Content $content): RedirectResponse
    {
        $content->deleted_by = auth()->id();
        $content->save();
        $content->delete();

        return redirect()->route('backend.contents.index')
            ->with('success', 'Content deleted successfully.');
    }
}
