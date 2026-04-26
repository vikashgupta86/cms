<?php

namespace Modules\Content\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Content\Entities\Content;
use Modules\Menu\Entities\Menu;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    public function show($path)
    {
        $menu = Menu::where('full_slug', $path)->firstOrFail();

        $children = Menu::where('parent_id', $menu->id)->get();

        if ($children->count()) {
            return view('content::frontend.child-menu-grid', compact('menu','children'));
        }

        $contents = Content::where('menu_id', $menu->id)
            ->where('status','published')
            ->paginate(10);

        return view('content::frontend.content-list', compact('menu','contents'));
    }

    public function showContent($path, $slug)
    {
        $menu = Menu::where('full_slug', $path)->firstOrFail();

        $content = Content::where('menu_id',$menu->id)
            ->where('slug',$slug)
            ->firstOrFail();

        if ($content->type === 'file') {
            return Storage::disk('public')->download($content->file_path);
        }

        if ($content->type === 'external') {
            return redirect()->away($content->external_url);
        }

        return view('content::frontend.content-detail', compact('content'));
    }
}
